<?php declare(strict_types=1);

namespace App\Criticalmass\Activity\Aspect;

use App\Criticalmass\Activity\RideData;

interface AspectInterface
{
    public function calculate(RideData $rideData): float;
}
