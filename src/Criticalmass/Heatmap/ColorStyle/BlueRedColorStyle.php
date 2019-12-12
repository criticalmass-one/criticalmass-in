<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\ColorStyle;

use Imagine\Image\Palette\Color\ColorInterface;

class BlueRedColorStyle extends AbstractColorStyle
{
    public function getStartColor(): ColorInterface
    {
        return $this->palette->color([0, 0, 255]);
    }

    public function colorize(ColorInterface $oldColor = null): ColorInterface
    {
        if (!$oldColor) {
            return $this->getStartColor();
        }

        $red = $oldColor->getValue(ColorInterface::COLOR_RED);
        $blue = $oldColor->getValue(ColorInterface::COLOR_BLUE);

        if ($red <= 205) {
            $red += 50;
        }

        if ($blue >= 50) {
            $blue -= 50;
        }

        return $this->palette->color([$red, 0, $blue]);
    }
 }
