<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Canvas;

use App\Criticalmass\Heatmap\DimensionCalculator\HeatmapDimension;
use App\Criticalmass\Heatmap\Tile\Tile;
use Caldera\GeoBasic\Coord\CoordInterface;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;

class Canvas
{
    /** @var int $width */
    protected $width;

    /** @var int $height */
    protected $height;

    /** @var CoordInterface $topLeftCoord */
    protected $topLeftCoord;

    /** @var int $topTileNumber */
    protected $topTileNumber;

    /** @var int $leftTileNumber */
    protected $leftTileNumber;

    protected $image;

    /** @var array $tiles */
    protected $tiles = [];

    public function __construct(int $width, int $height, CoordInterface $topLeftCoord = null, int $topTileNumber = null, int $leftTileNumber = null)
    {
        $this->width = $width;
        $this->height = $height;

        $this->topTileNumber = $topTileNumber;
        $this->leftTileNumber = $leftTileNumber;

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

        $point = new Point(($x - $this->leftTileNumber) * Tile::SIZE, ($y - $this->topTileNumber) * Tile::SIZE);
        $this->image->paste($tile->image(), $point);

        return $this;
    }

    public function getTopTileNumber(): int
    {
        return $this->topTileNumber;
    }

    public function setTopTileNumber(int $topTileNumber): Canvas
    {
        $this->topTileNumber = $topTileNumber;

        return $this;
    }

    public function getLeftTileNumber(): int
    {
        return $this->leftTileNumber;
    }

    public function setLeftTileNumber(int $leftTileNumber): Canvas
    {
        $this->leftTileNumber = $leftTileNumber;

        return $this;
    }
}
