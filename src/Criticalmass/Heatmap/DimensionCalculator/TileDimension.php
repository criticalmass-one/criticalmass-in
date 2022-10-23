<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\DimensionCalculator;

class TileDimension
{
    public function __construct(protected float $tileTopLatitude, protected float $tileLeftLongitude, protected float $tileBottomLatitude, protected float $tileRightLongitude)
    {
    }

    public function getTileTopLatitude(): float
    {
        return $this->tileTopLatitude;
    }

    public function getTileLeftLongitude(): float
    {
        return $this->tileLeftLongitude;
    }

    public function getTileBottomLatitude(): float
    {
        return $this->tileBottomLatitude;
    }

    public function getTileRightLongitude(): float
    {
        return $this->tileRightLongitude;
    }
}
