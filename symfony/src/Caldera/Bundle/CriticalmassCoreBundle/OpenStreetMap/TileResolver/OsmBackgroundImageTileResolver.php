<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\OpenStreetMap\TileResolver;

use Caldera\Bundle\CriticalmassCoreBundle\OpenStreetMap\Tile\OsmBackgroundImageTile;

class OsmBackgroundImageTileResolver extends OsmTileResolver
{
    static public function getTile($x, $y, $z)
    {
        $tile = new OsmBackgroundImageTile();

        $tile
            ->setPositionLeft($x)
            ->setPositionTop($y)
            ->setZoomLevel($z);

        return $tile;
    }
}