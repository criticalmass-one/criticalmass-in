<?php

namespace AppBundle\Command;

use AppBundle\Entity\City;
use AppBundle\Statistic\RideEstimate\RideEstimateService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InitAuditCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('criticalmass:audit:init')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $repo = $doctrine->getRepository('AppBundle:City');
        $manager = $doctrine->getManager();

        $cities = $repo->findCities();

        /** @var City $city */
        foreach ($cities as $city) {
            $output->writeln(sprintf('City: <info>%s</info>', $city->getCity()));

            $manager->detach($city);
            $manager->persist($city);

            $revs = $repo->findBy(['archiveParent' => $city, 'isArchived' => true]);

            /** @var City $rev */
            foreach ($revs as $rev) {
                $output->writeln(sprintf('Revision: <comment>%s</comment>', $rev->getCity()));

                $rev->setId($city->getId());

                $manager->merge($rev);
            }
        }

        $manager->flush();
    }
}