<?php declare(strict_types=1);

namespace App\Geo\Track;

class Track implements TrackInterface
{
    protected ?string $polyline = null;

    public function getPolyline(): string
    {
        return $this->polyline ?? '';
    }

    public function setPolyline(string $polyline): TrackInterface
    {
        $this->polyline = $polyline;

        return $this;
    }
}
