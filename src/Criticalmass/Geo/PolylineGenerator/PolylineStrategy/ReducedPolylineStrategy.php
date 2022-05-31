<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\PolylineGenerator\PolylineStrategy;

use PointReduction\Algorithms\RadialDistance;
use PointReduction\Common\Point;

class ReducedPolylineStrategy implements PolylineStrategyInterface
{
    const TOLERANCE = 0.05;

    public function generate(array $pointList): string
    {
        $reducer = new RadialDistance($pointList);

        $reducedPointList = $reducer->reduce(self::TOLERANCE);
        $reducedList = [];

        /** @var Point $point */
        foreach ($reducedPointList as $point) {
            $reducedList[] = [$point->x, $point->y];
        }

        return \Polyline::Encode($reducedList);
    }
}