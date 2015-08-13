<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Statistic\RideEstimate;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\RideEstimate;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;

class RideEstimateService
{
    protected $doctrine;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function calculateEstimates(Ride $ride)
    {
        $estimates = $this->doctrine->getRepository('CalderaCriticalmassModelBundle:RideEstimate')->findByRide($ride->getId());

        $rec = new RideEstimateCalculator();
        $rec->setRide($ride);
        $rec->setEstimates($estimates);
        $rec->calculate();
        $ride = $rec->getRide();

        $em = $this->doctrine;
        $em->persist($ride);
        $em->flush();
    }

    public function addEstimate(Track $track)
    {
        /* Extract the ride distance and duration into a RideEstimate entity. */
        $re = new RideEstimate();
        $re->setRide($track->getRide());
        $re->setUser($track->getUser());
        $re->setTrack($track);

        $track->setRideEstimate($re);

        $re->setEstimatedDistance($track->getDistance());
        $re->setEstimatedDuration($track->getDuration());

        $em = $this->doctrine;
        $em->persist($re);
        $em->flush();
    }
}