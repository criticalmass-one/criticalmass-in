<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Statistic\RideEstimateCalculator;

use AppBundle\Entity\Ride;

interface RideEstimateCalculatorInterface
{
    public function calculate(): RideEstimateCalculatorInterface;
    public function setEstimates(array $estimates = []): RideEstimateCalculatorInterface;
    public function getRide(): Ride;
    public function setRide(Ride $ride): RideEstimateCalculatorInterface;
}
