<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\OpenStreetMap\TileGrid;

use Caldera\Bundle\CriticalmassCoreBundle\OpenStreetMap\TileResolver\OsmBackgroundImageTileResolver;

class OsmBackgroundImageTileGrid extends OsmTileGrid
{
    public function fill()
    {
        for ($x = 0; $x < $this->width; ++$x) {
            for ($y = 0; $y < $this->height; ++$y) {
                $this->grid[$x][$y] = OsmBackgroundImageTileResolver::getTile($this->leftPosition + $x, $this->topPosition + $y, $this->zoomLevel);
            }
        }
    }
}