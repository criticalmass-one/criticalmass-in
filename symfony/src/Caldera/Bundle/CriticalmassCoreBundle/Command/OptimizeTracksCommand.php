<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngListGenerator\RangeLatLngListGenerator;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OptimizeTracksCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('criticalmass:tracks:optimize')
            ->setDescription('Regenerate LatLng Tracks')
            /*->addArgument(
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
                'Use to create the rides, otherwise you only get a preview')*/
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var Registry $doctrine
         */
        $doctrine = $this->getContainer()->get('doctrine');

        $tracks = $doctrine->getRepository('CalderaCriticalmassModelBundle:Track')->findAll();

        /**
         * @var RangeLatLngListGenerator $rlllg
         */
        $rlllg = $this->getContainer()->get('caldera.criticalmass.gps.latlnglistgenerator.range');

        /**
         * @var Track $track
         */
        foreach ($tracks as $track) {
            $rlllg->loadTrack($track);
            $rlllg->execute();

            $latLngList = $rlllg->getList();
            $track->setLatLngList($latLngList);

            /**
             * @var EntityManager $em
             */
            $em = $doctrine->getManager();
            $em->persist($track);
            $em->flush();

        }


    }
}