<?php

namespace App\Criticalmass\Geo\TimeShifter;

use Caldera\GeoBundle\PositionList\PositionListInterface;

abstract class AbstractTimeShifter implements TimeShifterInterface
{
    /** @var PositionListInterface $positionList */
    protected $positionList;

    public function setPositionList(PositionListInterface $positionList): TimeShifterInterface
    {
        $this->positionList = $positionList;

        return $this;
    }

    public function getPositionList(): PositionListInterface
    {
        return $this->positionList;
    }
}
