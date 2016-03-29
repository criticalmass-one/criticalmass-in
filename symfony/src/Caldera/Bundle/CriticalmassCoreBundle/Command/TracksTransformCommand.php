<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CriticalmassCoreBundle\StandardRideGenerator\StandardRideGenerator;
use Caldera\Bundle\CriticalmassCoreBundle\Statistic\RideEstimate\RideEstimateService;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TracksTransformCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('criticalmass:tracks:transform')
            ->setDescription('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tracks = $this->getContainer()->get('doctrine')->getRepository('CalderaCriticalmassModelBundle:Track')->findAll();

        $em = $this->getContainer()->get('doctrine')->getManager();

        /**
         * @var Track $track
         */
        foreach ($tracks as $track)
        {
            $output->writeln('Track #'.$track->getId());

            $array = json_decode($track->getLatLngList());

            if (is_array($array) and count($array) > 0) {
                $polyline = \Polyline::Encode($array);

                $track->setPolyline($polyline);

                $output->writeln($polyline);
                
                $em->persist($track);

            }
        }

        $em->flush();
    }
}