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

    /** @var float $topLatitude */
    protected $topLatitude;

    /** @var float $bottomLatitude */
    protected $bottomLatitude;

    /** @var float $leftLongitude */
    protected $leftLongitude;

    /** @var float $rightLongitude */
    protected $rightLongitude;

    /** @var float $leftOffset */
    protected $leftOffset;

    /** @var float $rightOffset */
    protected $rightOffset;

    /** @var float $topOffset */
    protected $topOffset;

    /** @var float $bottomOffset */
    protected $bottomOffset;

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

    public function getTopLatitude(): float
    {
        return $this->topLatitude;
    }

    public function setTopLatitude(float $topLatitude): HeatmapDimension
    {
        $this->topLatitude = $topLatitude;

        return $this;
    }

    public function getBottomLatitude(): float
    {
        return $this->bottomLatitude;
    }

    public function setBottomLatitude(float $bottomLatitude): HeatmapDimension
    {
        $this->bottomLatitude = $bottomLatitude;

        return $this;
    }

    public function getLeftLongitude(): float
    {
        return $this->leftLongitude;
    }

    public function setLeftLongitude(float $leftLongitude): HeatmapDimension
    {
        $this->leftLongitude = $leftLongitude;

        return $this;
    }

    public function getRightLongitude(): float
    {
        return $this->rightLongitude;
    }

    public function setRightLongitude(float $rightLongitude): HeatmapDimension
    {
        $this->rightLongitude = $rightLongitude;

        return $this;
    }

    public function getLeftOffset(): float
    {
        return $this->leftOffset;
    }

    public function setLeftOffset(float $leftOffset): HeatmapDimension
    {
        $this->leftOffset = $leftOffset;

        return $this;
    }

    public function getRightOffset(): float
    {
        return $this->rightOffset;
    }

    public function setRightOffset(float $rightOffset): HeatmapDimension
    {
        $this->rightOffset = $rightOffset;

        return $this;
    }

    public function getTopOffset(): float
    {
        return $this->topOffset;
    }

    public function setTopOffset(float $topOffset): HeatmapDimension
    {
        $this->topOffset = $topOffset;

        return $this;
    }

    public function getBottomOffset(): float
    {
        return $this->bottomOffset;
    }

    public function setBottomOffset(float $bottomOffset): HeatmapDimension
    {
        $this->bottomOffset = $bottomOffset;

        return $this;
    }

    public function getWidth(): int
    {
        return $this->rightTile - $this->leftTile + 1;
    }

    public function getHeight(): int
    {
        return $this->bottomTile - $this->topTile + 1;
    }
}
