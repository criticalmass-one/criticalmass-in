<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap;

class Tile
{
    /** @var int $tileX */
    protected $tileX;

    /** @var int $tileY */
    protected $tileY;

    /** @var int $zoomLevel */
    protected $zoomLevel;

    protected $image;

    public function __construct(int $tileX, int $tileY, int $zoomLevel)
    {
        $this->tileX = $tileX;
        $this->tileY = $tileY;
        $this->zoomLevel = $zoomLevel;
    }

    public function getTileX(): int
    {
        return $this->tileX;
    }

    public function getTileY(): int
    {
        return $this->tileY;
    }

    public function getZoomLevel(): int
    {
        return $this->zoomLevel;
    }
}
