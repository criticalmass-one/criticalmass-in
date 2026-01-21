<?php declare(strict_types=1);

namespace App\EntityInterface;

use App\Criticalmass\Geo\Coord\CoordInterface;

interface CoordinateInterface
{
    public function setLatitude(float $latitude = null): CoordinateInterface;

    public function getLatitude(): ?float;

    public function setLongitude(float $longitude = null): CoordinateInterface;

    public function getLongitude(): ?float;

    public function toCoord(): CoordInterface;
}
