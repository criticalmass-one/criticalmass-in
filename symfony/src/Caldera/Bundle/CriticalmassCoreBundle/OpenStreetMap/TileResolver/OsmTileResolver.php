<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\OpenStreetMap\TileResolver;

use Caldera\Bundle\CriticalmassCoreBundle\OpenStreetMap\Tile\OsmTile;

class OsmTileResolver
{
    public static function createTile($x, $y, $z)
    {
        $tile = new OsmTile($x, $y, $z);
        $tile->setUrl('http://a.tile.openstreetmap.org/'.$z.'/'.$x.'/'.$y.'.png');
        
        return $tile;
    }
}