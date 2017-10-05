<?php

namespace AppBundle\Command;

use AppBundle\CityCycleRideGenerator\CityCycleRideGenerator;
use AppBundle\Entity\City;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StandardRideCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('criticalmass:standardrides')
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
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var int $year */
        $year = $input->getArgument('year');

        /** @var int $month */
        $month = $input->getArgument('month');

        $generator = $this->getContainer()->get('app.city_cycle_ride_generator');
        $generator
            ->setMonth($month)
            ->setYear($year);

        $doctrine = $this->getContainer()->get('doctrine');

        $cities = $doctrine->getRepository('AppBundle:City')->findBy(
            [
                'enabled' => true
            ]
        );

        /** @var City $city */
        foreach ($cities as $city) {
            $output->writeln(sprintf('Stadt: <info>%s</info>', $city->getCity()));

            $rides = $generator
                ->setCity($city)
                ->execute()
                ->getList()
            ;

            if (count($rides)) {
                foreach ($rides as $ride) {
                    $output->writeln(sprintf('Tour: <comment>%s</comment> (%s)', $ride->getDateTime()->format('Y-m-d H:i'), $ride->getLocation()));
                }
            } else {
                $output->writeln('No rides for this city.');
            }
        }
    }
}