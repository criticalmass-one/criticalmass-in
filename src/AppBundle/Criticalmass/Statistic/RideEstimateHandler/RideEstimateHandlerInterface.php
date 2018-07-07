<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Statistic\RideEstimateHandler;

use AppBundle\Entity\Ride;
use AppBundle\Entity\Track;

interface RideEstimateHandlerInterface
{
    public function setRide(Ride $ride): RideEstimateHandlerInterface;
    public function flushEstimates(): RideEstimateHandlerInterface;
    public function calculateEstimates(): RideEstimateHandlerInterface;
    public function addEstimateFromTrack(Track $track): RideEstimateHandlerInterface;
}
