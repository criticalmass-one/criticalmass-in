<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Brush;

use App\Criticalmass\Heatmap\Tile\Tile;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\PointInterface;

class Pencil
{
    private function __construct()
    {

    }

    public static function paint(Tile $tile, PointInterface $point, ColorInterface $color): void
    {
        $tile->image()->draw()->dot($point, $color);
    }
}