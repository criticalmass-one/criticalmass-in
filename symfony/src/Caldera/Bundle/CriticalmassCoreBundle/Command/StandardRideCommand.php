<?php

namespace Caldera\CriticalmassStandardridesBundle\Command;

use Caldera\CriticalmassStandardridesBundle\Utility\StandardRideGenerator\StandardRideGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Use to create the rides, otherwise you only get a preview')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $year = $input->getArgument('year');
        $month = $input->getArgument('month');

        $cities = $this->getContainer()->get('doctrine')->getRepository('CalderaCriticalmassCoreBundle:City')->findBy(array('isArchived' => false, 'enabled' => true), array('city' => 'ASC'));
        
        foreach ($cities as $city)
        {
            $output->writeln($city->getTitle());

            if ($city->getIsStandardable()) {
                $srg = new StandardRideGenerator($city, $year, $month);
                $ride = $srg->execute();

                if ($srg->isRideDuplicate()) {
                    $output->writeln('Tour existiert bereits.');
                } else {
                    $output->writeln('Lege folgende Tour an');

                    if ($ride->getHasTime()) {
                        $output->writeln('Datum und Uhrzeit: ' . $ride->getDateTime()->format('Y-m-d H:i'));
                    } else {
                        $output->writeln('Datum: ' . $ride->getDateTime()->format('Y-m-d') . ', Uhrzeit ist bislang unbekannt');
                    }

                    if ($ride->getHasLocation()) {
                        $output->writeln('Treffpunkt: ' . $ride->getLocation() . ' (' . $ride->getLatitude() . '/' . $ride->getLongitude() . ')');
                    } else {
                        $output->writeln('Treffpunkt ist bislang unbekannt');
                    }

                    $output->writeln('sichtbar von ' . $ride->getVisibleSince()->format('Y-m-d H:i') . ' bis ' . $ride->getVisibleUntil()->format('Y-m-d H:i'));

                    $output->writeln('');
                    $output->writeln('');
                    
                    if ($input->hasOption('force')) {
                        $em = $this->getContainer()->get('doctrine')->getManager();
                        $em->persist($ride);
                        $em->flush();
                    }
                }
            }
            else
            {
                $output->writeln('Lege keine Tourdaten für diese Stadt an.');
            }
        }

        if (!$input->hasOption('force')) {
            $output->writeln('This was only a preview. Use --force to create rides.');
        }
    }
}