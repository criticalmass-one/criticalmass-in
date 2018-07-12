<?php declare(strict_types=1);

namespace App\Criticalmass\Statistic\RideEstimateHandler;

use App\Entity\RideEstimate;
use App\Entity\Track;

class RideEstimateHandler extends AbstractRideEstimateHandler
{
    public function flushEstimates(bool $flush = true): RideEstimateHandlerInterface
    {
        $this->ride
            ->setEstimatedDistance(0.0)
            ->setEstimatedDuration(0.0)
            ->setEstimatedParticipants(0);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $this;
    }

    public function calculateEstimates(bool $flush = true): RideEstimateHandlerInterface
    {
        $estimates = $this->getRideEstimateRepository()->findByRide($this->ride);

        $this->calculator
            ->setRide($this->ride)
            ->setEstimates($estimates)
            ->calculate();

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $this;
    }

    public function addEstimateFromTrack(Track $track, bool $flush = true): RideEstimateHandlerInterface
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

            if ($flush) {
                $this->getEntityManager()->flush();
            }
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
