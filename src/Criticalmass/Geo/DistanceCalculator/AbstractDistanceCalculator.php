<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\DistanceCalculator;


use App\Criticalmass\Geo\PositionList\PositionListInterface;

abstract class AbstractDistanceCalculator implements DistanceCalculatorInterface
{
    /** @var PositionListInterface $positionList */
    protected $positionList;

    public function setPositionList(PositionListInterface $positionList): DistanceCalculatorInterface
    {
        $this->positionList = $positionList;

        return $this;
    }
}
