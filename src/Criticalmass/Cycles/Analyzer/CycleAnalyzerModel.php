<?php declare(strict_types=1);

namespace App\Criticalmass\Cycles\Analyzer;

use App\Entity\CityCycle;
use App\Entity\Ride;

class CycleAnalyzerModel
{
    /** @var CityCycle $cycle */
    protected $cycle;

    /** @var Ride $ride */
    protected $ride;

    /** @var Ride $generatedRide */
    protected $generatedRide;

    public function __construct(CityCycle $cycle = null, Ride $ride, Ride $generatedRide = null)
    {
        $this->cycle = $cycle;
        $this->ride = $ride;
        $this->generatedRide = $generatedRide;
    }

    public function getCycle(): ?CityCycle
    {
        return $this->cycle;
    }

    public function getRide(): ?Ride
    {
        return $this->ride;
    }

    public function getGeneratedRide(): ?Ride
    {
        return $this->generatedRide;
    }

    public function compare(): int
    {
        $result = ComparisonResultInterface::EQUAL;

        if (!$this->ride || !$this->generatedRide) {
            $result += ComparisonResultInterface::NO_RIDE;

            return $result;
        }

        if ($this->ride->getLocation() === $this->generatedRide->getLocation()) {
            $result += ComparisonResultInterface::LOCATION_MISMATCH;
        }

        if ($this->ride->getDateTime() !== $this->generatedRide->getDateTime()) {
            $result += ComparisonResultInterface::DATETIME_MISMATCH;
        }

        return $result;
    }
}
