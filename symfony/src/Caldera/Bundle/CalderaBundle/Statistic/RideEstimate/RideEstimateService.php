<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Statistic\RideEstimate;

use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\RideEstimate;
use Caldera\Bundle\CalderaBundle\Entity\Track;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxReader\GpxReader;
use Doctrine\ORM\EntityManager;

class RideEstimateService
{
    /**
     * @var EntityManager $entityManager
     */
    protected $entityManager;

    /**
     * @var RideEstimateCalculator $calculator
     */
    protected $calculator;

    /**
     * @var GpxReader $reader
     */
    protected $reader;

    public function __construct(
        EntityManager $entityManager,
        RideEstimateCalculator $calculator,
        GpxReader $reader
    )
    {
        $this->entityManager = $entityManager;
        $this->calculator = $calculator;
        $this->reader = $reader;
    }

    public function flushEstimates(Ride $ride)
    {
        $ride->setEstimatedDistance(0.0);
        $ride->setEstimatedDuration(0.0);
        $ride->setEstimatedParticipants(0.0);

        $this->entityManager->persist($ride);
        $this->entityManager->flush();
    }

    public function calculateEstimates(Ride $ride)
    {
        $estimates = $this->entityManager->getRepository('CalderaBundle:RideEstimate')->findByRide($ride->getId());

        $rec = new RideEstimateCalculator();
        $rec->setRide($ride);

        $rec->setEstimates($estimates);
        $rec->calculate();

        $ride = $rec->getRide();

        $this->entityManager->persist($ride);
        $this->entityManager->flush();
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

        $durationInHours = (float)$track->getDurationInSeconds() / 3600.0;

        $re->setEstimatedDuration($durationInHours);

        $this->entityManager->persist($re);
        $this->entityManager->flush();
    }

    public function refreshEstimate(RideEstimate $re)
    {
        $track = $re->getTrack();
        $this->reader->loadTrack($track);

        $re->setEstimatedDistance($this->reader->calculateDistance());
        $re->setEstimatedDuration($this->reader->calculateDuration());
    }
}