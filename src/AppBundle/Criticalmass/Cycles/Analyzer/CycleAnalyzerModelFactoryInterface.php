<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Cycles\Analyzer;

interface CycleAnalyzerModelFactoryInterface
{
    public function setRides(array $rides): CycleAnalyzerModelFactoryInterface;
    public function setSimulatedRides(array $simulatedRides): CycleAnalyzerModelFactoryInterface;
    public function build(): CycleAnalyzerModelFactoryInterface;
    public function getResultList(): array;
}
