<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Criticalmass\Cycles\Analyzer;

use Criticalmass\Bundle\AppBundle\Entity\City;

interface CycleAnalyzerInterface
{
    public function setCity(City $city): CycleAnalyzerInterface;
    public function analyze(): CycleAnalyzerInterface;
    public function getResultList(): array;
}
