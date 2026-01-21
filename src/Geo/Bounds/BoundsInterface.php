<?php declare(strict_types=1);

namespace App\Geo\Bounds;

use App\Geo\Coord\CoordInterface;

interface BoundsInterface
{
    public function getNorthWest(): CoordInterface;
    public function getSouthEast(): CoordInterface;

    public function getNorthEast(): CoordInterface;
    public function getSouthWest(): CoordInterface;

    public function toLatLngArray(): array;
    public function toLatLonArray(): array;
}
