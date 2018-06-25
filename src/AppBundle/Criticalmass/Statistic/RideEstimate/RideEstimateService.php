<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Statistic\RideEstimate;

use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEstimate;
use AppBundle\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class RideEstimateService
{
    /** @var Doctrine $doctrine */
    protected $doctrine;

    /** @var RideEstimateCalculator $calculator */
    protected $calculator;

    public function __construct(Doctrine $doctrine, RideEstimateCalculator $calculator)
    {
        $this->doctrine = $doctrine;
        $this->calculator = $calculator;
    }

    public function flushEstimates(Ride $ride): RideEstimateService
    {
        $ride
            ->setEstimatedDistance(0.0)
            ->setEstimatedDuration(0.0)
            ->setEstimatedParticipants(0);

        $this->doctrine->getManager()->flush();

        return $this;
    }

    public function calculateEstimates(Ride $ride): RideEstimateService
    {
        $estimates = $this->doctrine->getRepository(RideEstimate::class)->findByRide($ride);

        $this->calculator
            ->setRide($ride)
            ->setEstimates($estimates)
            ->calculate();

        $this->doctrine->getManager()->flush();

        return $this;
    }

    public function addEstimateFromTrack(Track $track): RideEstimateService
    {
        $re = new RideEstimate();
        $re
            ->setRide($track->getRide())
            ->setUser($track->getUser())
            ->setTrack($track)
            ->setEstimatedDistance($track->getDistance())
            ->setEstimatedDuration($this->calculateDurationInHours($track));

        $track->setRideEstimate($re);

        $this->doctrine->getManager()->persist($re);
        $this->doctrine->getManager()->flush();

        return $this;
    }

    protected function calculateDurationInSeconds(Track $track): int
    {
        if ($track->getStartDateTime() && $track->getEndDateTime()) {
            return $track->getEndDateTime()->getTimestamp() - $track->getStartDate()->getTimestamp();
        }

        return 0;
    }

    protected function calculateDurationInHours(Track $track): float
    {
        if ($durationInSeconds = $this->calculateDurationInSeconds($track)) {
            return $durationInSeconds / 3600.0;
        }

        return 0;
    }
}
