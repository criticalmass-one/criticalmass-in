<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Pipette;

use App\Criticalmass\Heatmap\Canvas\Canvas;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\PointInterface;

class Pipette
{
    private function __construct()
    {

    }

    public static function getColor(Canvas $canvas, PointInterface $point): ColorInterface
    {
        return $canvas->image()->getColorAt($point);
    }
}
