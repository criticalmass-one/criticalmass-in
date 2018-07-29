<?php

namespace Caldera\GeoBundle\DistanceCalculator;

use Caldera\GeoBasic\Coord\CoordInterface;
use Caldera\GeoBasic\PolylineConverter\PolylineConverter;
use Caldera\GeoBasic\Track\TrackInterface;
use Caldera\GeoBundle\PositionList\PositionListInterface;

abstract class AbstractDistanceCalculator implements DistanceCalculatorInterface
{
    /** @var PositionListInterface $positionList */
    protected $positionList;

    public function setPositionList(PositionListInterface $positionList): DistanceCalculatorInterface
    {
        $this->positionList = $positionList;

        return $this;
    }
}
