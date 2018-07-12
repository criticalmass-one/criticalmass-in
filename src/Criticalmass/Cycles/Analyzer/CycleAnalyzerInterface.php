<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Cycles\Analyzer;

use AppBundle\Entity\City;

interface CycleAnalyzerInterface
{
    public function setCity(City $city): CycleAnalyzerInterface;
    public function analyze(): CycleAnalyzerInterface;
    public function getResultList(): array;
}
