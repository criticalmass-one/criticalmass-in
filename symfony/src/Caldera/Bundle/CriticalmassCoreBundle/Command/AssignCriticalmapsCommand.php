<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxExporter\GpxExporter;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxReader\TrackReader;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngListGenerator\RangeLatLngListGenerator;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngListGenerator\SimpleLatLngListGenerator;
use Caldera\Bundle\CriticalmassCoreBundle\Statistic\RideEstimate\RideEstimateService;
use Caldera\Bundle\CriticalmassModelBundle\Entity\CriticalmapsUser;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ticket;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;
use Caldera\Bundle\CriticalmassModelBundle\Repository\PositionRepository;
use Caldera\Bundle\CriticalmassModelBundle\Repository\RideRepository;
use Caldera\CriticalmassStatisticBundle\Utility\Heatmap\Position;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AssignCriticalmapsCommand extends ContainerAwareCommand
{
    /**
     * @var Registry $doctrine
     */
    protected $doctrine;

    /**
     * @var RangeLatLngListGenerator $generator
     */
    protected $generator;

    /**
     * @var EntityManager $manager
     */
    protected $manager;

    protected function configure()
    {
        $this
            ->setName('criticalmass:assign:criticalmaps')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $em = $this->doctrine->getManager();

        /**
         * @var ObjectRepository $criticalmapsUserRepository
         */
        $criticalmapsUserRepository = $this->doctrine->getRepository('CalderaCriticalmassModelBundle:CriticalmapsUser');

        /**
         * @var RideRepository $rideRepository
         */
        $rideRepository = $this->doctrine->getRepository('CalderaCriticalmassModelBundle:Ride');

        /**
         * @var PositionRepository $positionRepository
         */
        $positionRepository = $this->doctrine->getRepository('CalderaCriticalmassModelBundle:Position');

        $criticalmapsUsers = $criticalmapsUserRepository->findBy(['ride' => null, 'city' => null]);

        /**
         * @var CriticalmapsUser $cmu
         */
        foreach ($criticalmapsUsers as $cmu) {
            /**
             * @var Position $position
             */
            $position = $positionRepository->findFirstPositionForCriticalmapsUser($cmu);

            /**
             * @var Ride $ride
             */
            if ($position) {
                $ride = $rideRepository->findRideByLatitudeLongitudeDateTime($position->getLatitude(), $position->getLongitude(), $position->getCreationDateTime());
            }

            if ($ride) {
                $output->writeln($cmu->getId().' gefunden: '.$ride->getCity()->getCity());
                $cmu->setRide($ride);
                $cmu->setCity($ride->getCity());
            } else {
                $output->writeln($cmu->getId().': kein Treffer');
            }

            $em->persist($cmu);
        }

        $em->flush();
    }
}