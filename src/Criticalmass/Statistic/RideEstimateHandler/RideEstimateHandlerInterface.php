<?php declare(strict_types=1);

namespace App\Criticalmass\Statistic\RideEstimateHandler;

use App\Entity\Ride;
use App\Entity\Track;

interface RideEstimateHandlerInterface
{
    public function setRide(Ride $ride): RideEstimateHandlerInterface;
    public function flushEstimates(bool $flush = true): RideEstimateHandlerInterface;
    public function calculateEstimates(bool $flush = true): RideEstimateHandlerInterface;
}
