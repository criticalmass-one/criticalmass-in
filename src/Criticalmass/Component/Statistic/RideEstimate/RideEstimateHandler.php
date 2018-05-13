<?php declare(strict_types=1);

namespace Criticalmass\Component\Statistic\RideEstimate;

use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\RideEstimate;
use Criticalmass\Bundle\AppBundle\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Registry;

class RideEstimateHandler
{
    /** @var Registry $registry */
    protected $registry;

    /** @var RideEstimateCalculator $calculator */
    protected $calculator;

    /** @var Ride $ride */
    protected $ride;

    public function __construct(Registry $registry, RideEstimateCalculator $calculator)
    {
        $this->registry = $registry;
        $this->calculator = $calculator;
    }

    public function setRide(Ride $ride): RideEstimateHandler
    {
        $this->ride = $ride;

        return $this;
    }

    public function flushEstimates(Ride $ride): RideEstimateHandler
    {
        $this->ride
            ->setEstimatedDistance(0.0)
            ->setEstimatedDuration(0.0)
            ->setEstimatedParticipants(0);

        $this->registry->getManager()->flush();

        return $this;
    }

    public function calculateEstimates(): RideEstimateHandler
    {
        $estimates = $this->registry->getRepository(RideEstimate::class)->findByRide($this->ride);

        $this->calculator
            ->setRide($this->ride)
            ->setEstimates($estimates)
            ->calculate();

        $this->registry->getManager()->flush();

        return $this;
    }

    public function addEstimateFromTrack(Track $track): RideEstimateHandler
    {
        $re = new RideEstimate();
        $re
            ->setRide($track->getRide())
            ->setUser($track->getUser())
            ->setTrack($track)
            ->setEstimatedDistance($track->getDistance())
            ->setEstimatedDuration($this->calculateDurationInHours($track));

        $track->setRideEstimate($re);

        $this->registry->getManager()->persist($re);
        $this->registry->getManager()->flush();

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
