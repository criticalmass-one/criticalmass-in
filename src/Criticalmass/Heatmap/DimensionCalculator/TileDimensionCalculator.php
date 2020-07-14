<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\DimensionCalculator;

use App\Criticalmass\Heatmap\CoordCalculator\CoordCalculator;
use App\Criticalmass\Heatmap\Tile\Tile;

class TileDimensionCalculator
{
    private function __construct()
    {

    }

    public static function calculate(Tile $tile): TileDimension
    {
        $tileTopLatitude = CoordCalculator::yTileToLatitude($tile->getTileY(), $tile->getZoomLevel());
        $tileLeftLongitude = CoordCalculator::xTileToLongitude($tile->getTileX(), $tile->getZoomLevel());
        $tileBottomLatitude = CoordCalculator::yTileToLatitude($tile->getTileY() + 1, $tile->getZoomLevel());
        $tileRightLongitude = CoordCalculator::xTileToLongitude($tile->getTileX() + 1, $tile->getZoomLevel());

        return new TileDimension($tileTopLatitude, $tileLeftLongitude, $tileBottomLatitude, $tileRightLongitude);
    }
}
