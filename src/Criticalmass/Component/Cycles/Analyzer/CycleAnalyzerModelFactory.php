<?php declare(strict_types=1);

namespace Criticalmass\Component\Cycles\Analyzer;

class CycleAnalyzerModelFactory implements CycleAnalyzerModelFactoryInterface
{
    protected $rides = [];

    protected $simulatedRides = [];

    public function setRides(array $rides): CycleAnalyzerModelFactoryInterface
    {
        $this->rides = $rides;

        return $this;
    }

    public function setSimulatedRides(array $simulatedRides): CycleAnalyzerModelFactoryInterface
    {
        $this->simulatedRides = $simulatedRides;

        return $this;
    }

    public function build(): CycleAnalyzerModelFactoryInterface
    {

    }

}
