<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Canvas;

use App\Criticalmass\Heatmap\DimensionCalculator\HeatmapDimension;
use App\Criticalmass\Heatmap\Tile\Tile;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class Canvas
{
    /** @var int $width */
    protected $width;

    /** @var int $height */
    protected $height;

    protected $image;

    public static function fromHeatmapDimension(HeatmapDimension $heatmapDimension): Canvas
    {
        return new Canvas($heatmapDimension->getWidth(), $heatmapDimension->getHeight());
    }

    public static function fromWidthHeight(int $width, int $height): Canvas
    {
        return new Canvas($width, $height);
    }

    private function __construct(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;

        $box = new Box(256 * $this->width, 256 * $this->height);
        $this->image = (new Imagine())->create($box);
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function image(): ImageInterface
    {
        return $this->image;
    }
}