<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Pipette;

use App\Criticalmass\Heatmap\Tile\Tile;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\PointInterface;

class Pipette
{
    private function __construct()
    {

    }

    public static function getColor(Tile $tile, PointInterface $point): ?ColorInterface
    {
        return $tile->oldImage()->getColorAt($point);
    }
}
