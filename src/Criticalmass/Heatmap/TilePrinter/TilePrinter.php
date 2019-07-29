<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\TilePrinter;

use App\Criticalmass\Heatmap\Tile\Tile;
use Imagine\Image\Palette\RGB as RGBPalette;
use Imagine\Image\PointInterface;

class TilePrinter
{
    public function paint(Tile $tile, PointInterface $point): void
    {
        $color = (new RGBPalette())->color('#FFFFFF');
        $tile->image()->draw()->dot($point, $color);
    }
}