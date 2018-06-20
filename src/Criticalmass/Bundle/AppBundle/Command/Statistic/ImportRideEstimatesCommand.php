<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Command\Statistic;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\CitySlug;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\RideEstimate;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportRideEstimatesCommand extends Command
{
    protected $citySlugs = [];

    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(?string $name = null, RegistryInterface $registry)
    {
        $this->registry = $registry;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:rideestimate:import')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->citySlugs = $this->registry->getRepository(CitySlug::class)->findAllIndexed();

        $importLines = $this->readFromStdin();

        $estimateList = [];

        foreach ($importLines as $line) {
            $estimateList[] = $this->parse($line);
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
    }

    protected function readFromStdin(): array
    {
        $lines = [];

        if (0 === ftell(STDIN)) {
            while (!feof(STDIN)) {
                $lines[] = fread(STDIN, 1024);
            }
        }

        return $lines;
    }

    protected function parse(string $line): ?RideEstimate
    {
        $pattern = '/([\sA-Za-z]+)(?:[\s-:]+)([0-9.]+)/';
        preg_match($pattern, $line, $matches);

        if (3 === count($matches)) {
            $citySlug = trim(strtolower($matches[1]));
            $participants = intval(str_replace('.', '', $matches[2]));

            if ($ride = $this->findRide($citySlug)) {
                $estimate = new RideEstimate();

                $estimate
                    ->setEstimatedParticipants($participants)
                    ->setRide($ride);

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

    protected function findRide(string $citySlug): ?Ride
    {
        $dateTime = new \DateTime('2016-05-20');

        if ($city = $this->findCityBySlug($citySlug)) {
            $rides = $this->registry->getRepository(Ride::class)->findByCityAndMonth($city, $dateTime);

            $ride = array_pop($rides);

            return $ride;
        }

        return null;
    }
}
