<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\DimensionCalculator;

class TileDimension
{
    /** @var float $tileTopLatitude */
    protected $tileTopLatitude;

    /** @var float $tileLeftLongitude */
    protected $tileLeftLongitude;

    /** @var float $tileBottomLatitude */
    protected $tileBottomLatitude;

    /** @var float $tileRightLongitude */
    protected $tileRightLongitude;

    public function __construct(float $tileTopLatitude, float $tileLeftLongitude, float $tileBottomLatitude, float $tileRightLongitude)
    {
        $this->tileTopLatitude = $tileTopLatitude;
        $this->tileLeftLongitude = $tileLeftLongitude;
        $this->tileBottomLatitude = $tileBottomLatitude;
        $this->tileRightLongitude = $tileRightLongitude;
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
