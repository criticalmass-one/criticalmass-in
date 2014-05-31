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

    public function getPath()
    {
        return '/Applications/XAMPP/htdocs/criticalmass/symfony/web/images/heatmap/'.$this->tile->getOsmZoom().'/'.$this->tile->getOsmXTile().'/';
    }

    public function getFilename()
    {
        return $this->tile->getOsmYTile().'.png';
    }

    public abstract function printTile();
} 