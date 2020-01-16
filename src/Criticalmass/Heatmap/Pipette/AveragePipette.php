<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Pipette;

use App\Criticalmass\Heatmap\Tile\Tile;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;

class AveragePipette extends Pipette
{
    public static function getColor(Tile $tile, PointInterface $point, int $radius = 1): ?ColorInterface
    {
        $colorList = [];

        for ($x = $point->getX() - $radius; $x <= $point->getX() + $radius; ++$x) {
            for ($y = $point->getY() - $radius; $y <= $point->getY() + $radius; ++$y) {
                try {
                    $color = $tile->oldImage()->getColorAt(new Point($x, $y));

                    if ($color->isOpaque()) {
                        $colorList[] = $color;
                    }
                } catch (\Exception $exception) {

                }
            }
        }

        if (0 === count($colorList)) {
            return null;
        }

        $averageColor = [
            ColorInterface::COLOR_RED => 0,
            ColorInterface::COLOR_GREEN => 0,
            ColorInterface::COLOR_BLUE => 0,
        ];

        /** @var ColorInterface $color */
        foreach ($colorList as $color) {
            $averageColor[ColorInterface::COLOR_RED] += $color->getValue(ColorInterface::COLOR_RED);
            $averageColor[ColorInterface::COLOR_GREEN] += $color->getValue(ColorInterface::COLOR_GREEN);
            $averageColor[ColorInterface::COLOR_BLUE] += $color->getValue(ColorInterface::COLOR_BLUE);
        }

        foreach ($averageColor as $component => $value) {
            $averageColor[$component] = (int) round($value / count($colorList));
        }

        return (new RGB())->color($averageColor);
    }
}
