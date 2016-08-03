<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Map\Tile;

abstract class AbstractTile
{
    /** @var integer $pixelWidth */
    protected $pixelWidth;

    /** @var integer $pixelHeight */
    protected $pixelHeight;

    /** @var integer $positionLeft */
    protected $positionLeft;

    /** @var integer $positionTop */
    protected $positionTop;

    /** @var integer $zoomLevel */
    protected $zoomLevel;

    /**
     * @return int
     */
    public function getZoomLevel()
    {
        return $this->zoomLevel;
    }

    /**
     * @param int $zoomLevel
     */
    public function setZoomLevel($zoomLevel)
    {
        $this->zoomLevel = $zoomLevel;

        return $this;
    }

    /**
     * @return int
     */
    public function getPixelWidth()
    {
        return $this->pixelWidth;
    }

    /**
     * @param int $pixelWidth
     */
    public function setPixelWidth($pixelWidth)
    {
        $this->pixelWidth = $pixelWidth;

        return $this;
    }

    /**
     * @return int
     */
    public function getPixelHeight()
    {
        return $this->pixelHeight;
    }

    /**
     * @param int $pixelHeight
     */
    public function setPixelHeight($pixelHeight)
    {
        $this->pixelHeight = $pixelHeight;

        return $this;
    }

    /**
     * @return int
     */
    public function getPositionLeft()
    {
        return $this->positionLeft;
    }

    /**
     * @param int $positionLeft
     */
    public function setPositionLeft($positionLeft)
    {
        $this->positionLeft = $positionLeft;

        return $this;
    }

    /**
     * @return int
     */
    public function getPositionTop()
    {
        return $this->positionTop;
    }

    /**
     * @param int $positionTop
     */
    public function setPositionTop($positionTop)
    {
        $this->positionTop = $positionTop;

        return $this;
    }
}