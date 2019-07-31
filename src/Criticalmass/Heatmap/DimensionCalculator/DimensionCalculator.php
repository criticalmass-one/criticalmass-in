<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\DimensionCalculator;

use App\Criticalmass\Heatmap\CoordCalculator\CoordCalculator;
use App\Criticalmass\Heatmap\Path\Path;
use App\Criticalmass\Heatmap\Path\PathList;
use Caldera\GeoBasic\Coord\Coord;

class DimensionCalculator
{
    private function __construct()
    {

    }

    public static function calculate(PathList $pathList, int $zoomLevel): HeatmapDimension
    {
        $heatmapDimension = new HeatmapDimension($zoomLevel);

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
            ->setTopTile(CoordCalculator::latitudeToYTile($maxLat, $zoomLevel))
            ->setBottomLatitude($minLat)
            ->setBottomTile(CoordCalculator::latitudeToYTile($minLat, $zoomLevel))
            ->setLeftLongitude($minLon)
            ->setLeftTile(CoordCalculator::longitudeToXTile($minLon, $zoomLevel))
            ->setRightLongitude($maxLon)
            ->setRightTile(CoordCalculator::longitudeToXTile($maxLon, $zoomLevel));

        return $heatmapDimension;
    }

    protected static function calculateOffset(): void
    {

    }
}
