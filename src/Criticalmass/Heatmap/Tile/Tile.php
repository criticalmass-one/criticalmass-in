<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Tile;

use Imagine\Image\ImageInterface;

class Tile
{
    /** @var int $tileX */
    protected $tileX;

    /** @var int $tileY */
    protected $tileY;

    /** @var int $zoomLevel */
    protected $zoomLevel;

    /** @var ImageInterface $image */
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

    public function image(): ImageInterface
    {
        return $this->image;
    }

    public function setImage(ImageInterface $image): Tile
    {
        $this->image = $image;

        return $this;
    }
}
