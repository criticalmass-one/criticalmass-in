<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\TrackDecider;

use App\Entity\Ride;

class RideResult
{
    /** @var Ride $ride */
    protected $ride;

    /** @var float $result */
    protected $result;

    public function __construct(Ride $ride, float $result)
    {
        $this->ride = $ride;
        $this->result = $result;
    }

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function getResult(): float
    {
        return $this->result;
    }
}
