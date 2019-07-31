<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Brush;

use App\Criticalmass\Heatmap\Canvas\Canvas;
use Imagine\Image\Box;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\PointInterface;

class Brush
{
    private function __construct()
    {

    }

    public static function paint(Canvas $canvas, PointInterface $point, ColorInterface $color): void
    {
        $box = new Box(2, 2);

        $canvas->image()->draw()->ellipse($point, $box, $color);
    }
}