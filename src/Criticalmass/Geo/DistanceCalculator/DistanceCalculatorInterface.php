<?php

namespace Caldera\GeoBundle\DistanceCalculator;

use Caldera\GeoBundle\PositionList\PositionListInterface;

interface DistanceCalculatorInterface
{
    public function setPositionList(PositionListInterface $positionList): DistanceCalculatorInterface;
    public function calculate(): float;
}
