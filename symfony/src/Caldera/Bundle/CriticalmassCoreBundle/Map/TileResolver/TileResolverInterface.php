<?php
namespace Caldera\Bundle\CriticalmassCoreBundle\Map\TileResolver;

use Caldera\Bundle\CriticalmassCoreBundle\Gps\Coord;

interface TileResolverInterface
{
    static public function getByCoord(Coord $coord, $zoomLevel);
    static public function getByLatitudeLongitude($latitude, $longitude, $zoomLevel);
    static public function getByTilePosition($top, $left, $zoomLevel);
    static public function getTile($x, $y, $z);
}