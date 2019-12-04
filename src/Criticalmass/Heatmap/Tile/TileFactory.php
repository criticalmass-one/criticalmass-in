<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Tile;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Palette\RGB as RGBPalette;

class TileFactory
{
    /** @var ImagineInterface $imagine */
    protected $imagine;

    public function __construct()
    {
        $this->imagine = (new Imagine());
    }

    public function create(int $tileX, int $tileY, int $zoomLevel, ImageInterface $oldImage = null): Tile
    {
        $tile = new Tile($tileX, $tileY, $zoomLevel);

        if (!$oldImage) {
            $box = new Box(Tile::SIZE, Tile::SIZE);
            $transparency = (new RGBPalette())->color('#FFFFFF', 0);
            $oldImage = $this->imagine->create($box, $transparency);
        }

        $tile
            ->setOldImage($oldImage)
            ->setNewImage($oldImage->copy());

        return $tile;
    }
}
