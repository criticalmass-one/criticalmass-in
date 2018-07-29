<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\TimeShifter;

use Caldera\GeoBundle\PositionList\PositionListInterface;

interface TimeShifterInterface
{
    public function setPositionList(PositionListInterface $positionList): TimeShifterInterface;
    public function getPositionList(): PositionListInterface;
    public function shift(\DateInterval $interval): TimeShifterInterface;
}
