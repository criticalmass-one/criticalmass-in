<?php

namespace Caldera\GeoBundle\TimeShifter;

use Caldera\GeoBundle\PositionList\PositionListInterface;

interface TimeShifterInterface
{
    public function setPositionList(PositionListInterface $positionList): TimeShifterInterface;
    public function getPositionList(): PositionListInterface;
    public function shift(\DateInterval $interval): TimeShifterInterface;
}
