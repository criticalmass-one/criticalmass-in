<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Loop;

use App\Criticalmass\Geo\EntityInterface\PositionInterface;
use App\Criticalmass\Geo\PositionList\PositionListInterface;

interface LoopInterface
{
    public function setPositionList(PositionListInterface $positionList): LoopInterface;
    public function setDateTimeZone(\DateTimeZone $dateTimeZone): LoopInterface;
    public function searchIndexForDateTime(\DateTimeInterface $dateTime): ?int;
    public function searchPositionForDateTime(\DateTime $dateTime): ?PositionInterface;
}
