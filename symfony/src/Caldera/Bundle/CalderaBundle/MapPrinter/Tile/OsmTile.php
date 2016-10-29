<?php

namespace Caldera\Bundle\CalderaBundle\MapPrinter\Tile;

class OsmTile implements TileInterface
{
    /** @var int $pixelWidth */
    protected $pixelWidth = 256;

    /** @var int $pixelHeight */
    protected $pixelHeight = 256;

    /** @var int $x */
    protected $x;

    /** @var int $y */
    protected $y;

    /** @var int $zoomLevel */
    protected $zoomLevel;

    public function __construct(int $x, int $y, int $zoomLevel)
    {
        $this->x = $x;
        $this->y = $y;
        $this->zoomLevel = $zoomLevel;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function getZoomLevel(): int
    {
        return $this->zoomLevel;
    }
}