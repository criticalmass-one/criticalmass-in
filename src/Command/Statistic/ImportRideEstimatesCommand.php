<?php declare(strict_types=1);

namespace App\Command\Statistic;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Ride;
use App\Entity\RideEstimate;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'criticalmass:rideestimate:import',
    description: 'Import ride estimates',
)]
class ImportRideEstimatesCommand extends Command
{
    protected $citySlugs = [];

    public function __construct(protected ManagerRegistry $registry)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('year', InputArgument::REQUIRED)
            ->addArgument('month', InputArgument::REQUIRED)
            ->addArgument('filename', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dateTimeSpec = sprintf('%d-%d-01', $input->getArgument('year'), $input->getArgument('month'));
        $dateTime = new \DateTime($dateTimeSpec);

        $this->citySlugs = $this->registry->getRepository(CitySlug::class)->findAllIndexed();

        $importLines = $this->readFromFile($input->getArgument('filename'));

        $estimateList = [];

        foreach ($importLines as $line) {
            if ($estimation = $this->parse($line, $dateTime, $input, $output)) {
                $estimateList[] = $estimation;
            }
        }

        $table = new Table($output);
        $table->setHeaders([
            'City',
            'DateTime',
            'Participants',
        ]);

        /** @var RideEstimate $estimation */
        foreach ($estimateList as $estimation) {
            $table->addRow([
                $estimation->getRide()->getCity()->getCity(),
                $estimation->getRide()->getDateTime()->format('Y-m-d H:i'),
                $estimation->getEstimatedParticipants(),
            ]);
        }

        $table->render();

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Save estimations?');

        $persist = $helper->ask($input, $output, $question);

        if ($persist) {
            foreach ($estimateList as $estimation) {
                $this->registry->getManager()->persist($estimation);
            }

            $this->registry->getManager()->flush();

            $output->writeln('Persisted estimations. Please recalculate now.');
        }

        return Command::SUCCESS;
    }

    protected function readFromFile(string $filename): array
    {
        $lines = file($filename);

        return $lines;
    }

    protected function parse(string $line, \DateTime $dateTime, InputInterface $input, OutputInterface $output): ?RideEstimate
    {
        $pattern = '/([\sA-Za-z\-.]+[a-z])(?:[\s\-–—:]+)([0-9.]+)/';
        preg_match($pattern, $line, $matches);

        if (3 === count($matches)) {
            $citySlug = trim(strtolower($matches[1]));
            $participants = intval(str_replace('.', '', $matches[2]));

            $ride = $this->findRide($citySlug, $dateTime);

            while (!$ride) {
                $question = new Question(sprintf('No ride found for city "%s", please provide a city slug or press enter to skip: ', $citySlug));

                $citySlug = $this->getHelper('question')->ask($input, $output, $question);

                if (!$citySlug) {
                    break;
                }

                $ride = $this->findRide($citySlug, $dateTime);
            }

            if ($ride) {
                $estimate = new RideEstimate();

                $estimate
                    ->setEstimatedParticipants($participants)
                    ->setRide($ride);

                if ($this->estimateExists($estimate)) {
                    return null;
                }

                return $estimate;
            }
        }

        return null;
    }

    protected function findCityBySlug(string $slug): ?City
    {
        if (array_key_exists($slug, $this->citySlugs)) {
            return $this->citySlugs[$slug]->getCity();
        }

        return null;
    }

    protected function findRide(string $citySlug, \DateTime $dateTime): ?Ride
    {
        if ($city = $this->findCityBySlug($citySlug)) {
            $rides = $this->registry->getRepository(Ride::class)->findByCityAndMonth($city, $dateTime);

            $ride = array_pop($rides);

            return $ride;
        }

        return null;
    }

    protected function estimateExists(RideEstimate $estimate): bool
    {
        return null !== $this->registry->getRepository(RideEstimate::class)->findByRideAndParticipants($estimate->getRide(), $estimate->getEstimatedParticipants());
    }
}
