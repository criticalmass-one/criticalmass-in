<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Tile;

use Imagine\Image\ImageInterface;

class Tile
{
    /** @const int SIZE */
    final const SIZE = 256;

    /** @var ImageInterface $oldImage */
    protected $oldImage;

    /** @var ImageInterface $newImage */
    protected $newImage;

    public function __construct(protected int $tileX, protected int $tileY, protected int $zoomLevel = null)
    {
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
