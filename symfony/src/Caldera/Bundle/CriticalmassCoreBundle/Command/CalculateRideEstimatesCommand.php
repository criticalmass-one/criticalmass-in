<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CriticalmassCoreBundle\StandardRideGenerator\StandardRideGenerator;
use Caldera\Bundle\CriticalmassCoreBundle\Statistic\RideEstimate\RideEstimateService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CalculateRideEstimatesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('criticalmass:rideestimate:recalculate')
            ->setDescription('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var RideEstimateService
         */
        $res = $this->getContainer()->get('caldera.criticalmass.statistic.rideestimate');
        
        $rides = $this->getContainer()->get('doctrine')->getRepository('CalderaCriticalmassModelBundle:Ride')->findEstimatedRides();
        
        foreach ($rides as $ride)
        {
            $output->writeln($ride->getCity()->getCity().': '.$ride->getFormattedDate());

            $res->flushEstimates($ride);
            $res->calculateEstimates($ride);
            
/*
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
            }*/
        }
    }
}