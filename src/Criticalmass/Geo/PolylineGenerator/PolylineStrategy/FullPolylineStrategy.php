<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\PolylineGenerator\PolylineStrategy;

class FullPolylineStrategy implements PolylineStrategyInterface
{
    public function generate(array $pointList): string
    {
        return \Polyline::Encode($pointList);
    }
}