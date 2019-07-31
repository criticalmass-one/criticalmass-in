<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Brush;

use App\Criticalmass\Heatmap\Canvas\Canvas;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\PointInterface;

class Pencil
{
    private function __construct()
    {

    }

    public static function paint(Canvas $canvas, PointInterface $point, ColorInterface $color): void
    {
        $canvas->image()->draw()->dot($point, $color);
    }
}