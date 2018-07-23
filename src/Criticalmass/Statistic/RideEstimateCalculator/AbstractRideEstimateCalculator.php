<?php declare(strict_types=1);

namespace App\Criticalmass\Statistic\RideEstimateCalculator;

use App\Entity\Ride;

abstract class AbstractRideEstimateCalculator implements RideEstimateCalculatorInterface
{
    /** @var Ride $ride */
    protected $ride;

    /** @var array $estimates */
    protected $estimates = [];

    public function setEstimates(array $estimates = []): RideEstimateCalculatorInterface
    {
        $this->estimates = $estimates;

        return $this;
    }

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride): RideEstimateCalculatorInterface
    {
        $this->ride = $ride;

        return $this;
    }
}
