<?php declare(strict_types=1);

namespace App\Criticalmass\Activity\Aspect;

use App\Criticalmass\Activity\RideData;

class TrackAspect extends AbstractAspect
{
    public function calculate(RideData $rideData): float
    {
        if ($rideData->getRide())
        return 0;
    }
}
