<?php declare(strict_types=1);

namespace App\Geo\Bounds;

use App\Geo\Coord\Coord;
use App\Geo\Coord\CoordInterface;

class Bounds implements BoundsInterface
{
    protected CoordInterface $northWest;
    protected CoordInterface $southEast;

    public function __construct(CoordInterface $northWest, CoordInterface $southEast)
    {
        $this->northWest = $northWest;
        $this->southEast = $southEast;
    }

    public function getNorthWest(): CoordInterface
    {
        return $this->northWest;
    }

    public function getSouthEast(): CoordInterface
    {
        return $this->southEast;
    }

    public function getNorthEast(): CoordInterface
    {
        return new Coord($this->northWest->getLatitude(), $this->southEast->getLongitude());
    }

    public function getSouthWest(): CoordInterface
    {
        return new Coord($this->southEast->getLatitude(), $this->northWest->getLongitude());
    }

    public function toLatLngArray(): array
    {
        return [
            [
                'lat' => $this->northWest->getLatitude(),
                'lng' => $this->northWest->getLongitude(),
            ],
            [
                'lat' => $this->southEast->getLatitude(),
                'lng' => $this->southEast->getLongitude(),
            ],
        ];
    }

    public function toLatLonArray(): array
    {
        return [
            [
                'lat' => $this->northWest->getLatitude(),
                'lon' => $this->northWest->getLongitude(),
            ],
            [
                'lat' => $this->southEast->getLatitude(),
                'lon' => $this->southEast->getLongitude(),
            ],
        ];
    }
}
