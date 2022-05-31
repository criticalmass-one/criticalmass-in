<?php declare(strict_types=1);

namespace App\Criticalmass\Statistic\RideEstimateHandler;

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
}
