<?php declare(strict_types=1);

namespace Criticalmass\Component\Cycles\Analyzer;

use Criticalmass\Bundle\AppBundle\Entity\City;

interface CycleAnalyzerInterface
{
    public function setCity(City $city): CycleAnalyzerInterface;
    public function analyze(): CycleAnalyzerInterface;
}
