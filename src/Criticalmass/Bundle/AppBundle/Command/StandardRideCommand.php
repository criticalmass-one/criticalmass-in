<?php

namespace Criticalmass\Bundle\AppBundle\Command;

use Criticalmass\Bundle\AppBundle\CityCycleRideGenerator\CityCycleRideGenerator;
use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StandardRideCommand extends ContainerAwareCommand
{
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

        $generator = $this->getContainer()->get('Criticalmass\Component\RideGenerator\RideGenerator\RideGenerator');
        $generator
            ->setMonth($month)
            ->setYear($year);

        $doctrine = $this->getContainer()->get('doctrine');
        $manager = $doctrine->getManager();

        $cities = $doctrine->getRepository('AppBundle:City')->findCities();

        $table = new Table($output);
        $table
            ->setHeaders(['City', 'DateTime', 'Location']);

        /** @var City $city */
        foreach ($cities as $city) {
            $rides = $generator
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
