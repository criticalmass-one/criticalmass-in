<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\OpenStreetMap\TileGrid;

use Caldera\Bundle\CriticalmassCoreBundle\OpenStreetMap\Tile\OsmTile;

class OsmTileGrid
{
    protected $grid;

    public function __construct($cols, $rows)
    {
        for ($x = 0; $x < $cols; ++$x) {
            for ($y = 0; $y < $rows; ++$y) {
                $this->grid[$x][$y] = 0;
            }
        }
    }

    public function placeTile(OsmTile $tile, $x, $y)
    {
        $this->grid[$x][$y] = $tile;
    }
}