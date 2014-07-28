<?php
/**
 * Created by PhpStorm.
 * User: Malte
 * Date: 31.05.14
 * Time: 16:26
 */

namespace Caldera\CriticalmassHeatmapBundle\Utility;

use Caldera\CriticalmassHeatmapBundle\Entity\Heatmap;

abstract class AbstractTilePrinter {
    protected $tile;
    protected $imageFileContent;
    protected $heatmap;

    public function __construct(Tile $tile, Heatmap $heatmap)
    {
        $this->tile = $tile;
        $this->heatmap = $heatmap;
    }

    public function getImageFileContent()
    {
        return $this->imageFileContent;
    }

    public function getPath()
    {
        return '/Applications/XAMPP/htdocs/criticalmass/symfony/web/images/heatmap/'.$this->heatmap->getIdentifier().'/'.$this->tile->getOsmZoom().'/'.$this->tile->getOsmXTile().'/';
    }

    public function getFilename()
    {
        return $this->tile->getOsmYTile().'.png';
    }

    public abstract function printTile();
} 