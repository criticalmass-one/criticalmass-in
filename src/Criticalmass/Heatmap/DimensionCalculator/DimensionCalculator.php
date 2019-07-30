<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\DimensionCalculator;

use App\Criticalmass\Heatmap\CoordCalculator\CoordCalculator;
use App\Criticalmass\Heatmap\Path\Path;
use App\Criticalmass\Heatmap\Path\PathList;

class DimensionCalculator
{
    private function __construct()
    {

    }

    public static function calculate(PathList $pathList, int $zoomLevel): HeatmapDimension
    {
        $heatmapDimension = new HeatmapDimension($zoomLevel);

        /** @var Path $path */
        $path = $pathList->get();

        $minLat = $path->getStartCoord()->getLatitude();
        $minLon = $path->getStartCoord()->getLongitude();
        $maxLat = $path->getStartCoord()->getLatitude();
        $maxLon = $path->getStartCoord()->getLongitude();

        while ($path !== null) {
            $endCoord = $path->getEndCoord();

            if ($endCoord->getLatitude() < $minLat) {
                $minLat = $endCoord->getLatitude();
            }

            if ($endCoord->getLongitude() < $minLon) {
                $minLon = $endCoord->getLongitude();
            }

            if ($endCoord->getLatitude() > $maxLat) {
                $maxLat = $endCoord->getLatitude();
            }

            if ($endCoord->getLongitude() > $maxLon) {
                $maxLon = $endCoord->getLongitude();
            }

            $path = $pathList->get();
        }

        $heatmapDimension
            ->setTopTile(CoordCalculator::latitudeToYTile($minLat, $zoomLevel))
            ->setBottomTile(CoordCalculator::latitudeToYTile($maxLat, $zoomLevel))
            ->setLeftTile(CoordCalculator::longitudeToXTile($minLon, $zoomLevel))
            ->setRightTile(CoordCalculator::longitudeToXTile($maxLon, $zoomLevel));

        return $heatmapDimension;
    }
}
