<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\DistanceCalculator;

use App\Criticalmass\Geo\PositionList\PositionListInterface;

interface DistanceCalculatorInterface
{
    public function setPositionList(PositionListInterface $positionList): DistanceCalculatorInterface;
    public function calculate(): float;
}
