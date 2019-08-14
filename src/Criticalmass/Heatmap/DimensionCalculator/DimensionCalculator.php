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

        $pathList->rewind();

        /** @var Path $path */
        $path = $pathList->current();

        $minLat = $path->getStartCoord()->getLatitude();
        $minLon = $path->getStartCoord()->getLongitude();
        $maxLat = $path->getStartCoord()->getLatitude();
        $maxLon = $path->getStartCoord()->getLongitude();

        while ($path) {
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

            $pathList->next();
            $path = $pathList->current();
        }

        $heatmapDimension
            ->setTopLatitude($maxLat)
            ->setBottomLatitude($minLat)
            ->setRightLongitude($maxLon)
            ->setLeftLongitude($minLon);

        $heatmapDimension
            ->setTopTile(CoordCalculator::latitudeToYTile($maxLat, $zoomLevel))
            ->setBottomTile(CoordCalculator::latitudeToYTile($minLat, $zoomLevel))
            ->setLeftTile(CoordCalculator::longitudeToXTile($minLon, $zoomLevel))
            ->setRightTile(CoordCalculator::longitudeToXTile($maxLon, $zoomLevel));

        return $heatmapDimension;
    }
}
