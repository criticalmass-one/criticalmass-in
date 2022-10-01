<?php declare(strict_types=1);

namespace App\Criticalmass\Activity\Aspect;

use App\Criticalmass\Activity\RideData;

class EstimationAspect extends AbstractAspect
{
    public function calculate(RideData $rideData): float
    {
        if ($rideData->getRide()->getEstimates()->count() > 0) {
            return 1;
        }

        return 0;
    }
}
