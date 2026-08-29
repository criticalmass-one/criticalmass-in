<?php declare(strict_types=1);

namespace App\Command\Cycles;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Ride;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'criticalmass:cycles:generate-rides',
    description: 'Generate rides from the city cycles for a given period of time',
)]
class GenerateRidesCommand extends Command
{
    private string $rideGeneratorBaseUrl;

    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly HttpClientInterface $httpClient,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        string $criticalmassRideGeneratorUrl,
    ) {
        $this->rideGeneratorBaseUrl = rtrim($criticalmassRideGeneratorUrl, '/');

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'First month to generate rides for, as YYYY-MM')
            ->addOption('until', null, InputOption::VALUE_REQUIRED, 'Last month to generate rides for, as YYYY-MM')
            ->addOption('city', null, InputOption::VALUE_REQUIRED, 'Limit the generation to a single city slug')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Show what would be created without touching the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $fromDate = $this->parseMonth($input->getOption('from'), false);
        $untilDate = $this->parseMonth($input->getOption('until'), true);

        if (!$fromDate || !$untilDate) {
            $io->error('Both --from and --until are required and must be given as YYYY-MM.');

            return Command::INVALID;
        }

        if ($fromDate > $untilDate) {
            $io->error('--from must not be later than --until.');

            return Command::INVALID;
        }

        $dryRun = (bool) $input->getOption('dry-run');

        $cityList = $this->fetchCities($input->getOption('city'), $fromDate, $untilDate);

        if (0 === count($cityList)) {
            $io->warning('No city with an active cycle found for this period.');

            return Command::SUCCESS;
        }

        $io->title(sprintf(
            'Generating rides for %s to %s (%d cities)%s',
            $fromDate->format('Y-m'),
            $untilDate->format('Y-m'),
            count($cityList),
            $dryRun ? ' — dry run' : ''
        ));

        $createdCount = 0;
        $skippedCount = 0;
        $failedCities = [];

        foreach ($cityList as $city) {
            $citySlug = $city->getMainSlug()?->getSlug();

            if (!$citySlug) {
                $io->writeln(sprintf('<comment>%s: no main slug, skipping</comment>', $city->getCity()));

                continue;
            }

            try {
                $rideList = $this->fetchRidePreview($citySlug, $fromDate, $untilDate);
            } catch (\Throwable $exception) {
                $failedCities[] = $citySlug;
                $io->writeln(sprintf('<error>%s: %s</error>', $citySlug, $exception->getMessage()));

                continue;
            }

            $created = 0;
            $skipped = 0;

            foreach ($rideList as [$ride, $cycle]) {
                // The generator walks whole months and, depending on the timezone offset of
                // the request, may hand back a ride of the month after untilDate.
                if ($ride->getDateTime() < $fromDate || $ride->getDateTime() > $untilDate) {
                    continue;
                }

                $ride->setCity($city);
                $ride->setCycle($cycle);
                $ride->setCreatedAt(new \DateTime());

                if (count($this->validator->validate($ride)) > 0) {
                    ++$skipped;

                    continue;
                }

                if (!$dryRun) {
                    $this->registry->getManager()->persist($ride);
                }

                ++$created;
            }

            // Flushing per city keeps the SingleRideForDay validator meaningful: it queries
            // the database, so rides of the previous city have to be written before the next
            // city is validated.
            if (!$dryRun && $created > 0) {
                $this->registry->getManager()->flush();
            }

            $createdCount += $created;
            $skippedCount += $skipped;

            $io->writeln(sprintf('%s: %d created, %d skipped', $citySlug, $created, $skipped));
        }

        $io->newLine();
        $io->success(sprintf(
            '%d rides %s, %d skipped as invalid or already existing.',
            $createdCount,
            $dryRun ? 'would be created' : 'created',
            $skippedCount
        ));

        if (0 < count($failedCities)) {
            $io->warning(sprintf('The ride generator failed for: %s', implode(', ', $failedCities)));

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function parseMonth(?string $month, bool $endOfMonth): ?\DateTime
    {
        if (!$month || 1 !== preg_match('/^\d{4}-\d{2}$/', $month)) {
            return null;
        }

        $dateTime = \DateTime::createFromFormat(
            'Y-m-d H:i:s',
            sprintf('%s-01 00:00:00', $month),
            new \DateTimeZone('UTC')
        );

        if (!$dateTime) {
            return null;
        }

        if ($endOfMonth) {
            $dateTime->modify('last day of this month')->setTime(23, 59, 59);
        }

        return $dateTime;
    }

    /**
     * @return list<City>
     */
    private function fetchCities(?string $citySlug, \DateTime $fromDate, \DateTime $untilDate): array
    {
        $queryBuilder = $this->registry->getManager()->createQueryBuilder()
            ->select('city')
            ->from(City::class, 'city')
            ->join(CityCycle::class, 'cycle', 'WITH', 'cycle.city = city')
            ->where('cycle.disabledAt IS NULL')
            ->andWhere('cycle.validFrom IS NULL OR cycle.validFrom <= :untilDate')
            ->andWhere('cycle.validUntil IS NULL OR cycle.validUntil >= :fromDate')
            ->setParameter('fromDate', $fromDate)
            ->setParameter('untilDate', $untilDate)
            ->groupBy('city.id')
            ->orderBy('city.city', 'ASC');

        if ($citySlug) {
            $queryBuilder
                ->join('city.slugs', 'slug')
                ->andWhere('slug.slug = :citySlug')
                ->setParameter('citySlug', $citySlug);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Asks the ride generator for the rides of one city and pairs every generated ride with
     * the cycle it originates from.
     *
     * @return list<array{0: Ride, 1: ?CityCycle}>
     */
    private function fetchRidePreview(string $citySlug, \DateTime $fromDate, \DateTime $untilDate): array
    {
        $response = $this->httpClient->request('POST', $this->rideGeneratorBaseUrl . '/api/preview', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'citySlug' => $citySlug,
                'fromDate' => $fromDate->format(\DateTimeInterface::ATOM),
                'untilDate' => $untilDate->format(\DateTimeInterface::ATOM),
            ],
        ]);

        $content = $response->getContent();

        /** @var list<Ride> $rideList */
        $rideList = $this->serializer->deserialize($content, Ride::class . '[]', 'json', [
            'groups' => ['api-write'],
        ]);

        $rawRideList = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        $result = [];

        foreach ($rideList as $index => $ride) {
            $cycleId = $rawRideList[$index]['cycle']['id'] ?? null;
            $cycle = $cycleId ? $this->registry->getRepository(CityCycle::class)->find($cycleId) : null;

            $result[] = [$ride, $cycle];
        }

        return $result;
    }
}
