<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Canvas;

use App\Criticalmass\Heatmap\DimensionCalculator\HeatmapDimension;
use App\Criticalmass\Heatmap\Tile\Tile;
use Caldera\GeoBasic\Coord\CoordInterface;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class Canvas
{
    /** @var int $width */
    protected $width;

    /** @var int $height */
    protected $height;

    /** @var CoordInterface $topLeftCoord */
    protected $topLeftCoord;

    protected $image;

    /** @var array $tiles */
    protected $tiles = [];

    public function __construct(int $width, int $height, CoordInterface $topLeftCoord = null)
    {
        $this->width = $width;
        $this->height = $height;

        $this->topLeftCoord = $topLeftCoord;

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

    public function getTopLeftCoord(): ?CoordInterface
    {
        return $this->topLeftCoord;
    }

    public function getTile(int $x, int $y): ?Tile
    {
        if (!array_key_exists($x, $this->tiles) || !array_key_exists($y, $this->tiles[$x])) {
            return null;
        }

        return $this->tiles[$x][$y];
    }

    public function setTile(int $x, int $y, Tile $tile): Canvas
    {
        if (!array_key_exists($x, $this->tiles)) {
            $this->tiles[$x] = [];
        }

        $this->tiles[$x][$y] = $tile;

        return $this;
    }
}
