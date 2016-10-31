<?php

namespace Caldera\Bundle\CalderaBundle\MapPrinter\TileResolver;

use Caldera\Bundle\CalderaBundle\MapPrinter\Coord\Coord;
use Caldera\Bundle\CalderaBundle\MapPrinter\Tile\TileInterface;

interface TileResolverInterface
{
    public function resolveByCoord(Coord $coord, int $zoomLevel): TileInterface;

    public function resolveByLatitudeLongitude(float $latitude, float $longitude, int $zoomLevel): TileInterface;

    public function resolveByZxy(int $x, int $y, int $zoomLevel): TileInterface;
}