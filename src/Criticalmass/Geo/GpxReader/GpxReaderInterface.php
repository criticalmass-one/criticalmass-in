<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\GpxReader;

use App\Criticalmass\Geo\EntityInterface\PositionInterface;
use App\Criticalmass\Geo\PositionList\PositionListInterface;

interface GpxReaderInterface
{
    public function loadFromString(string $gpxString): GpxReaderInterface;

    public function loadFromFile(string $filename): GpxReaderInterface;

    public function getCreationDateTime(): \DateTime;

    public function getStartDateTime(): \DateTime;

    public function getEndDateTime(): \DateTime;

    public function countPoints(): int;

    public function getLatitudeOfPoint(int $n): float;

    public function getLongitudeOfPoint(int $n): float;

    public function getElevationOfPoint(int $n): float;

    public function getDateTimeOfPoint(int $n): \DateTime;

    public function getPoint(int $n): \SimpleXMLElement;
}
