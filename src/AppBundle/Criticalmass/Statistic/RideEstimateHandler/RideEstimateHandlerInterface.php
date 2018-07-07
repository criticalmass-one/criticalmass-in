<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Statistic\RideEstimateHandler;

use AppBundle\Entity\Ride;
use AppBundle\Entity\Track;

interface RideEstimateHandlerInterface
{
    public function setRide(Ride $ride): RideEstimateHandlerInterface;
    public function flushEstimates(bool $flush = true): RideEstimateHandlerInterface;
    public function calculateEstimates(bool $flush = true): RideEstimateHandlerInterface;
    public function addEstimateFromTrack(Track $track): RideEstimateHandlerInterface;
}
