<?php

namespace AppBundle\Command;

use AppBundle\Entity\City;
use AppBundle\Entity\CityCycle;
use AppBundle\Statistic\RideEstimate\RideEstimateService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InitCyclesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('criticalmass:cycles:init')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $repo = $doctrine->getRepository('AppBundle:City');
        $manager = $doctrine->getManager();

        $cities = $repo->findBy(['isStandardable' => true]);

        /** @var City $city */
        foreach ($cities as $city) {
            $output->writeln(sprintf('City: <info>%s</info>', $city->getCity()));

            $cityCycle = new CityCycle();

            $cityCycle
                ->setCity($city)
                ->setDayOfWeek($city->getStandardDayOfWeek())
                ->setWeekOfMonth($city->getStandardWeekOfMonth())
                ->setLocation($city->getStandardLocation())
                ->setLatitude($city->getStandardLatitude())
                ->setLongitude($city->getStandardLongitude())
                ->setTime($city->getStandardTime())
            ;

            $manager->persist($cityCycle);
        }

        $manager->flush();
    }
}