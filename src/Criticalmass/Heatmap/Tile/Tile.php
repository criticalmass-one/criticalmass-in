<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Tile;

use Imagine\Image\ImageInterface;

class Tile
{
    /** @const int SIZE */
    const SIZE = 256;

    /** @var int $tileX */
    protected $tileX;

    /** @var int $tileY */
    protected $tileY;

    /** @var int $zoomLevel */
    protected $zoomLevel;

    /** @var ImageInterface $oldImage */
    protected $oldImage;

    /** @var ImageInterface $newImage */
    protected $newImage;

    public function __construct(int $tileX, int $tileY, int $zoomLevel = null)
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

    public function getZoomLevel(): ?int
    {
        return $this->zoomLevel;
    }

    public function oldImage(): ImageInterface
    {
        return $this->oldImage;
    }

    public function newImage(): ImageInterface
    {
        return $this->newImage;
    }

    public function setOldImage(ImageInterface $oldImage): Tile
    {
        $this->oldImage = $oldImage;

        return $this;
    }

    public function setNewImage(ImageInterface $newImage): Tile
    {
        $this->newImage = $newImage;

        return $this;
    }

    public function __destruct()
    {
        unset($this->newImage);
        unset($this->oldImage);
    }
}
