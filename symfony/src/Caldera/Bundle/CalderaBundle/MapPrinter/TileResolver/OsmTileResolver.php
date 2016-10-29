<?php

namespace Caldera\Bundle\CalderaBundle\MapPrinter\TileResolver;

use Caldera\Bundle\CalderaBundle\MapPrinter\Coord\Coord;
use Caldera\Bundle\CalderaBundle\MapPrinter\Tile\OsmTile;
use Caldera\Bundle\CalderaBundle\MapPrinter\Tile\TileInterface;
use Caldera\Bundle\CalderaBundle\MapPrinter\Util\OsmZxyCalculator;

class OsmTileResolver implements TileResolverInterface
{
    public function resolveByCoord(Coord $coord, int $zoomLevel): TileInterface
    {
        return $this->resolveByLatitudeLongitude($coord->getLatitude(), $coord->getLongitude(), $zoomLevel);
    }

    public function resolveByLatitudeLongitude(float $latitude, float $longitude, int $zoomLevel): TileInterface
    {
        $osmX = OsmZxyCalculator::longitudeToOSMXTile($latitude, $zoomLevel);
        $osmY = OsmZxyCalculator::latitudeToOSMYTile($longitude, $zoomLevel);
        
        return $this->resolveByZxy($osmX, $osmY, $zoomLevel);  
    }

    public function resolveByZxy(int $x, int $y, int $zoomLevel): TileInterface
    {
        $tile = new OsmTile();
        
        return
    }
}