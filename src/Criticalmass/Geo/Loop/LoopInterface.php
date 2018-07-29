<?php

namespace Caldera\GeoBundle\Loop;

use Caldera\GeoBundle\PositionList\PositionListInterface;

interface LoopInterface
{
    public function setPositionList(PositionListInterface $positionList): LoopInterface;
    public function setDateTimeZone(\DateTimeZone $dateTimeZone): LoopInterface;
    public function searchIndexForDateTime(\DateTimeInterface $dateTime): int;
}
