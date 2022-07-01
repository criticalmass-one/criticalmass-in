<?php declare(strict_types=1);

namespace App\Criticalmass\Activity;

use App\Entity\Ride;

class RideData
{
    protected Ride $ride;
    protected float $result = 0.0;

    public function __construct(Ride $ride)
    {
        $this->ride = $ride;
    }

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function addResult(float $result): self
    {
        $this->result += $result;

        return $this;
    }

    public function setResult(float $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function getResult(): float
    {
        return $this->result;
    }
}
