<?php

namespace Caldera\Bundle\CalderaBundle\Manager\Util;

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
}