<?php declare(strict_types=1);

namespace App\Criticalmass\Activity\Aspect;

use App\Criticalmass\Activity\RideData;

class MonthlyRideAspect extends AbstractAspect
{
    public function calculate(RideData $rideData): float
    {
        return 0;
    }
}
