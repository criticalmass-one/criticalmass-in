<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Loop;

use Caldera\GeoBundle\PositionList\PositionListInterface;

interface LoopInterface
{
    public function setPositionList(PositionListInterface $positionList): LoopInterface;
    public function setDateTimeZone(\DateTimeZone $dateTimeZone): LoopInterface;
    public function searchIndexForDateTime(\DateTimeInterface $dateTime): int;
}
