<?php

namespace App\Manager\Util;

/**
 * @deprecated
 */
class Bounds
{
    /** @var Coord $northWest */
    protected $northWest;

    /** @var Coord $southEast */
    protected $southEast;

    public function __construct(Coord $northWest, Coord $southEast)
    {
        $this->northWest = $northWest;
        $this->southEast = $southEast;
    }

    public function toLatLonArray()
    {
        return [$this->northWest->toLatLonArray(), $this->southEast->toLatLonArray()];
    }
}
