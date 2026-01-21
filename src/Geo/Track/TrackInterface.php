<?php declare(strict_types=1);

namespace App\Geo\Track;

interface TrackInterface
{
    public function getPolyline(): string;
    public function setPolyline(string $polyline): TrackInterface;
}
