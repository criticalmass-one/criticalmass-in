<?php declare(strict_types=1);

namespace Criticalmass\Component\Cycles\Analyzer;

use Criticalmass\Bundle\AppBundle\Entity\CityCycle;
use Criticalmass\Bundle\AppBundle\Entity\Ride;

class CycleAnalyzerModel
{
    /** @var CityCycle $cycle */
    protected $cycle;

    /** @var Ride $ride */
    protected $ride;

    /** @var Ride $generatedRide */
    protected $generatedRide;

    public function __construct(CityCycle $cycle, Ride $ride, Ride $generatedRide)
    {
        $this->cycle = $$cycle;
        $this->ride = $ride;
        $this->generatedRide = $generatedRide;
    }

    public function getCycle(): CityCycle
    {
        return $this->cycle;
    }

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function getGeneratedRide(): Ride
    {
        return $this->generatedRide;
    }

}
