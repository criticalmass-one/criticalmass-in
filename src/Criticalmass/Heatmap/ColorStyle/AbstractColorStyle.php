<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\ColorStyle;

use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\PaletteInterface;
use Imagine\Image\Palette\RGB as RGBPalette;

abstract class AbstractColorStyle implements ColorStyleInterface
{
    /** @var PaletteInterface $palette */
    protected $palette;

    public function __construct()
    {
        $this->palette = new RGBPalette();
    }
}