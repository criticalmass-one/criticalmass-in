<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\ColorStyle;

use Imagine\Image\Palette\Color\ColorInterface;

interface ColorStyleInterface
{
    public function colorize(ColorInterface $color = null): ColorInterface;
    public function getStartColor(): ColorInterface;
}
