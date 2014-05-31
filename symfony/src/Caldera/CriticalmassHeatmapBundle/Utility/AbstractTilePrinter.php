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
    protected $imageFileContent;

    public function __construct(Tile $tile)
    {
        $this->tile = $tile;
    }

    public function getImageFileContent()
    {
        return $this->imageFileContent;
    }

    public abstract function printTile();
} 