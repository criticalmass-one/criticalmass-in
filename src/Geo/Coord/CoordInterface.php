<?php declare(strict_types=1);

namespace App\Geo\Coord;

interface CoordInterface
{
    public function getLatitude(): ?float;
    public function getLongitude(): ?float;
}
