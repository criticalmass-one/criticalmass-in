<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\PolylineGenerator\PolylineStrategy;

interface PolylineStrategyInterface
{
    public function generate(array $pointList): string;
}