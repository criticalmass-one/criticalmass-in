<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Map\TileGridPrinter;

use Caldera\Bundle\CriticalmassCoreBundle\Map\TileGrid\TileGrid;

class TileGridPrinter
{
    /** @var TileGrid $tileGrid */
    protected $tileGrid;

    protected $image;
    
    public function __construct()
    {
        
    }
    
    public function setGrid(TileGrid $tileGrid)
    {
        $this->tileGrid = $tileGrid;

        return $this;
    }
    
    public function createGridImage()
    {
        $this->image = imagecreate($this->tileGrid->getWidth() * 256, $this->tileGrid->getHeight() * 256);

        return $this;
    }
    
    public function copyTilesToGridImage()
    {
        for ($x = 0; $x < $this->tileGrid->getWidth(); ++$x) {
            for ($y = 0; $y < $this->tileGrid->getHeight(); ++$y) {
                $tile = $this->tileGrid->getTile($x, $y);

                echo $tile->getBackgroundImageUrl()."FOOO";
            }
        }

        return $this;
    }

    public function save()
    {
        imagepng($this->image, '/var/www/criticalmass.cm/symfony/web/foo.png');
    }
}