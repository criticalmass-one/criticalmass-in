<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Statistic\RideEstimateHandler;

use AppBundle\Entity\RideEstimate;
use AppBundle\Entity\Track;

class RideEstimateHandler extends AbstractRideEstimateHandler
{
    public function flushEstimates(): RideEstimateHandlerInterface
    {
        $this->ride
            ->setEstimatedDistance(0.0)
            ->setEstimatedDuration(0.0)
            ->setEstimatedParticipants(0);

        $this->getEntityManager()->flush();

        return $this;
    }

    public function calculateEstimates(): RideEstimateHandlerInterface
    {
        $estimates = $this->getRideEstimateRepository()->findByRide($this->ride);

        $this->calculator
            ->setRide($this->ride)
            ->setEstimates($estimates)
            ->calculate();

        $this->getEntityManager()->flush();

        return $this;
    }

    public function addEstimateFromTrack(Track $track): RideEstimateHandlerInterface
    {
        if ($track->getRideEstimate()) {
            $re = $track->getRideEstimate();
        } else {
            $re = new RideEstimate();
            $re
                ->setRide($track->getRide())
                ->setUser($track->getUser())
                ->setTrack($track)
                ->setEstimatedDistance($track->getDistance())
                ->setEstimatedDuration($this->calculateDurationInHours($track));

            $track->setRideEstimate($re);

            $this->getEntityManager()->persist($re);
            $this->getEntityManager()->flush();
        }

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
