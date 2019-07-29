<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\DimensionCalculator;

class HeatmapDimension
{
    /** @var int $zoomLevel */
    protected $zoomLevel;

    /** @var int $topTile */
    protected $topTile;

    /** @var int $bottomTile */
    protected $bottomTile;

    /** @var int $leftTile */
    protected $leftTile;

    /** @var int $rightTile */
    protected $rightTile;

    public function __construct(?int $zoomLevel)
    {
        $this->zoomLevel = $zoomLevel;
    }

    public function getZoomLevel(): int
    {
        return $this->zoomLevel;
    }

    public function setZoomLevel(int $zoomLevel): HeatmapDimension
    {
        $this->zoomLevel = $zoomLevel;

        return $this;
    }

    public function getTopTile(): int
    {
        return $this->topTile;
    }

    public function setTopTile(int $topTile): HeatmapDimension
    {
        $this->topTile = $topTile;

        return $this;
    }

    public function getBottomTile(): int
    {
        return $this->bottomTile;
    }

    public function setBottomTile(int $bottomTile): HeatmapDimension
    {
        $this->bottomTile = $bottomTile;

        return $this;
    }

    public function getLeftTile(): int
    {
        return $this->leftTile;
    }

    public function setLeftTile(int $leftTile): HeatmapDimension
    {
        $this->leftTile = $leftTile;

        return $this;
    }

    public function getRightTile(): int
    {
        return $this->rightTile;
    }

    public function setRightTile(int $rightTile): HeatmapDimension
    {
        $this->rightTile = $rightTile;

        return $this;
    }
}
