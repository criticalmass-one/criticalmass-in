<?php

namespace Caldera\GeoBundle\GpxReader;

use Caldera\GeoBundle\EntityInterface\PositionInterface;
use Caldera\GeoBundle\PositionList\PositionListInterface;

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

    public function createPosition(int $n): PositionInterface;

    public function createPositionList(): PositionListInterface;
}
