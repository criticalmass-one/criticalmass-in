<?php
/**
 * Created by PhpStorm.
 * User: maltehuebner
 * Date: 02.08.16
 * Time: 23:01
 */

namespace Caldera\Bundle\CriticalmassCoreBundle\Gps;


class BoundingBox
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