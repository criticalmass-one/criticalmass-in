<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Map\TileGrid;

class TileGrid
{
    /** @var array $grid */
    protected $grid;

    /** @var integer $width */
    protected $width;

    /** @var integer $height */
    protected $height;

    /** @var integer $leftPosition */
    protected $leftPosition;

    /** @var integer $topPosition */
    protected $topPosition;

    /** @var integer $zoomLevel */
    protected $zoomLevel;

    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;

        for ($x = 0; $x < $width; ++$x) {
            for ($y = 0; $y < $height; ++$y) {
                $this->grid[$x][$y] = null;
            }
        }
    }

    public function setTopPosition($topPosition)
    {
        $this->topPosition = $topPosition;

        return $this;
    }

    public function setLeftPosition($leftPosition)
    {
        $this->leftPosition = $leftPosition;

        return $this;
    }

    public function setZoomLevel($zoomLevel)
    {
        $this->zoomLevel = $zoomLevel;

        return $this;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getTile($x, $y)
    {
        return $this->grid[$x][$y];
    }
}