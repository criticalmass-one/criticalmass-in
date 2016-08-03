<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\OpenStreetMap\TileResolver;

use Caldera\Bundle\CriticalmassCoreBundle\Gps\Coord;
use Caldera\Bundle\CriticalmassCoreBundle\Map\TileResolver\AbstractTileResolver;
use Caldera\Bundle\CriticalmassCoreBundle\OpenStreetMap\Tile\OsmTile;
use Caldera\Bundle\CriticalmassCoreBundle\OpenStreetMap\Util\OsmCoordCalculator;

class OsmTileResolver extends AbstractTileResolver
{
    protected $url = 'http://{s}.tile.osm.org/{z}/{x}/{y}.png';

    static public function getByCoord(Coord $coord, $zoomLevel)
    {
        $top = OsmCoordCalculator::latitudeToOSMYTile($coord->getLatitude(), $zoomLevel);
        $left = OsmCoordCalculator::longitudeToOSMXTile($coord->getLongitude(), $zoomLevel);

        return self::getByTilePosition($top, $left, $zoomLevel);
    }

    static public function getByTilePosition($top, $left, $zoomLevel)
    {
        return self::getTile($left, $top, $zoomLevel);
    }
    
    static public function getTile($x, $y, $z)
    {
        $tile = new OsmTile();

        $tile
            ->setPositionLeft($x)
            ->setPositionTop($y)
            ->setZoomLevel($z);

        return $tile;
    }
}