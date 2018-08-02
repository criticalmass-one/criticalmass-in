<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\TimeShifter;

use App\Criticalmass\Geo\PositionList\PositionListInterface;

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
