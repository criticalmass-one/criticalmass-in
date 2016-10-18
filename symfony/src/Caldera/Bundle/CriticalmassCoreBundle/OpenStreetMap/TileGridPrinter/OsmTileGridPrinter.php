<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\OpenStreetMap\TileGridPrinter;

use Caldera\Bundle\CriticalmassCoreBundle\OpenStreetMap\TileGrid\OsmTileGrid;

class OsmTileGridPrinter
{
    /** @var OsmTileGrid $tileGrid */
    protected $tileGrid;

    public function __construct()
    {
    }

    public function setTileGrid(OsmTileGrid $tileGrid)
    {
        $this->tileGrid = $tileGrid;
    }
}