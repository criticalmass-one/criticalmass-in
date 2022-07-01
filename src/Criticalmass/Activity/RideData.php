<?php declare(strict_types=1);

namespace App\Criticalmass\Activity;

use App\Entity\Ride;

class RideData
{
    protected Ride $ride;

    public function __construct(Ride $ride)
    {
        $this->ride = $ride;
    }

    public function getRide(): Ride
    {
        return $this->ride;
    }
}
