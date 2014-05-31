<?php
/**
 * Created by PhpStorm.
 * User: Malte
 * Date: 31.05.14
 * Time: 16:26
 */

namespace Caldera\CriticalmassHeatmapBundle\Utility;


abstract class AbstractTilePrinter {
    protected $tile;

    public function __construct(Tile $tile)
    {
        $this->tile = $tile;
    }

    public abstract function printTile();
} 