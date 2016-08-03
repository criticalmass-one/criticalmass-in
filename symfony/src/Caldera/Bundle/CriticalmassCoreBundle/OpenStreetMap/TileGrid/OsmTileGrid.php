<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\OpenStreetMap\TileGrid;

use Caldera\Bundle\CriticalmassCoreBundle\Map\TileGrid\TileGrid;
use Caldera\Bundle\CriticalmassCoreBundle\OpenStreetMap\TileResolver\OsmTileResolver;

class OsmTileGrid extends TileGrid
{
    public function fill()
    {
        for ($x = 0; $x < $this->width; ++$x) {
            for ($y = 0; $y < $this->height; ++$y) {
                $this->grid[$x][$y] = OsmTileResolver::getTile($this->leftPosition + $x, $this->topPosition + $y, $this->zoomLevel);
            }
        }
    }
}