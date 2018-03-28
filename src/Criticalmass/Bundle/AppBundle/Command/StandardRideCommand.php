<?php

namespace Criticalmass\Bundle\AppBundle\Command;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Component\RideGenerator\RideGenerator\RideGeneratorInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StandardRideCommand extends Command
{
    /** @var RideGeneratorInterface $rideGenerator */
    protected $rideGenerator;

    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct($name = null, RideGeneratorInterface $rideGenerator, RegistryInterface $registry)
    {
        $this->rideGenerator = $rideGenerator;
        $this->registry = $registry;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('criticalmass:cycles:create')
            ->setDescription('Create rides for a parameterized year and month automatically')
            ->addArgument(
                'year',
                InputArgument::REQUIRED,
                'Year of the rides to create'
            )
            ->addArgument(
                'month',
                InputArgument::REQUIRED,
                'Month of the rides to create'
            )
            ->addOption(
                'save',
                null,
                InputOption::VALUE_NONE,
                'Save the generated stuff'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var int $year */
        $year = $input->getArgument('year');

        /** @var int $month */
        $month = $input->getArgument('month');

        $this->rideGenerator
            ->setMonth($month)
            ->setYear($year);

        $manager = $this->registry->getManager();

        $cities = $this->registry->getRepository(City::class)->findCities();

        $table = new Table($output);
        $table
            ->setHeaders(['City', 'DateTime', 'Location']);

        /** @var City $city */
        foreach ($cities as $city) {
            $rides = $this->rideGenerator
                ->setCity($city)
                ->execute()
                ->getList();

            if (count($rides)) {
                /** @var Ride $ride */
                foreach ($rides as $ride) {
                    $table->addRow([
                        $city->getCity(),
                        $ride->getDateTime()->format('Y-m-d H:i'),
                        $ride->getLocation(),
                    ]);

                    $manager->persist($ride);
                }
            }
        }

        $table->render();

        if ($input->getOption('save')) {
            $output->writeln('Saved all those rides');

            $manager->flush();
        } else {
            $output->writeln('Did not save any of these rides, run with --save to persist.');
        }
    }
}
