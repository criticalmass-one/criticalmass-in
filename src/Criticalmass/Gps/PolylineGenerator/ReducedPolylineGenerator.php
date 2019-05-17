<?php declare(strict_types=1);

namespace App\Criticalmass\Gps\PolylineGenerator;

use PointReduction\Algorithms\RadialDistance;
use PointReduction\Common\Point;

/**
 * @deprecated
 */
class ReducedPolylineGenerator extends AbstractPolylineGenerator
{
    public function execute(float $tolerance = 0.005): PolylineGeneratorInterface
    {
        $list = array_values($this->trackReader->slicePublicCoords());

        $reducer = new RadialDistance($list);

        $reducedPointList = $reducer->reduce($tolerance);
        $reducedList = [];

        /** @var Point $point */
        foreach ($reducedPointList as $point) {
            $reducedList[] = [$point->x, $point->y];
        }

        $this->polyline = \Polyline::Encode($reducedList);

        return $this;
    }
} 
