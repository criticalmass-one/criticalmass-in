<?php

namespace Caldera\CriticalmassStatisticBundle\Service;

use Caldera\CriticalmassCoreBundle\Entity\Ride;
use Caldera\CriticalmassStatisticBundle\Utility\RideEstimateCalculator\RideEstimateCalculator;

class RideEstimateService
{
    protected $doctrine;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function calculateEstimates(Ride $ride)
    {
        $estimates = $this->doctrine->getRepository('CalderaCriticalmassStatisticBundle:RideEstimate')->findByRide($ride->getId());

        $rec = new RideEstimateCalculator();
        $rec->setRide($ride);
        $rec->setEstimates($estimates);
        $rec->calculate();
        $ride = $rec->getRide();

        $em = $this->doctrine;
        $em->persist($ride);
        $em->flush();
    }
}