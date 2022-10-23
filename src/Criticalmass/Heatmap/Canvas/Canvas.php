<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Canvas;

use App\Criticalmass\Heatmap\Tile\Tile;
use Caldera\GeoBasic\Coord\CoordInterface;

class Canvas
{
    /** @var array $tiles */
    protected $tiles = [];

    public function __construct(protected int $width, protected int $height, protected CoordInterface $topLeftCoord = null, protected int $topTileNumber = null, protected int $leftTileNumber = null)
    {
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
