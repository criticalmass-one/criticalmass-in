<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Canvas;

use App\Criticalmass\Heatmap\Tile\Tile;
use Caldera\GeoBasic\Coord\CoordInterface;

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

    /** @var array $tiles */
    protected $tiles = [];

    public function __construct(int $width, int $height, CoordInterface $topLeftCoord = null, int $topTileNumber = null, int $leftTileNumber = null)
    {
        $this->width = $width;
        $this->height = $height;

        $this->topTileNumber = $topTileNumber;
        $this->leftTileNumber = $leftTileNumber;

        $this->topLeftCoord = $topLeftCoord;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
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

    public function __destruct()
    {
        for ($x = $this->leftTileNumber; $x < $this->leftTileNumber + $this->width; ++$x) {
            for ($y = $this->topTileNumber; $y < $this->topTileNumber + $this->height; ++$y) {
                unset($this->tiles[$x][$y]);
            }
        }
    }
}
