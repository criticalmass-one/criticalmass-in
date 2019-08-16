<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Brush;

use App\Criticalmass\Heatmap\Tile\Tile;
use Imagine\Image\Box;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\PointInterface;

class Brush
{
    private function __construct()
    {

    }

    public static function paint(Tile $tile, PointInterface $point, ColorInterface $color): void
    {
        $box = new Box(2, 2);

        $tile->newImage()->draw()->ellipse($point, $box, $color, true);
    }
}