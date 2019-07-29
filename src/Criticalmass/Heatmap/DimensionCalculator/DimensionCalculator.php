<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\DimensionCalculator;

use App\Criticalmass\Heatmap\CoordCalculator\CoordCalculator;

class DimensionCalculator
{
    protected $pathArray;
    protected $zoom;

    /** @var HeatmapDimension $heatmapDimension */
    protected $heatmapDimension;

    public function __construct($pathArray, int $zoomLevel)
    {
        $this->heatmapDimension = new HeatmapDimension($zoomLevel);
        $this->pathArray = $pathArray;

        $path = array_shift($this->pathArray);

        $minLat = $path->getStartPosition()->getLatitude();
        $minLon = $path->getStartPosition()->getLongitude();
        $maxLat = $path->getStartPosition()->getLatitude();
        $maxLon = $path->getStartPosition()->getLongitude();

        while ($path != null) {
            if ($path->getEndPosition()->getLatitude() < $minLat) {
                $minLat = $path->getEndPosition()->getLatitude();
            }

            if ($path->getEndPosition()->getLongitude() < $minLon) {
                $minLon = $path->getEndPosition()->getLongitude();
            }

            if ($path->getEndPosition()->getLatitude() > $maxLat) {
                $maxLat = $path->getEndPosition()->getLatitude();
            }

            if ($path->getEndPosition()->getLongitude() > $maxLon) {
                $maxLon = $path->getEndPosition()->getLongitude();
            }

            $path = array_shift($this->pathArray);
        }

        $this->heatmapDimension
            ->setTopTile(CoordCalculator::latitudeToYTile($minLat, $this->zoom))
            ->setBottomTile(CoordCalculator::latitudeToYTile($maxLat, $this->zoom))
            ->setLeftTile(CoordCalculator::longitudeToXTile($minLon, $this->zoom))
            ->setRightTile(CoordCalculator::longitudeToXTile($maxLon, $this->zoom));
    }

    public function getHeatmapDimension(): HeatmapDimension
    {
        return $this->heatmapDimension;
    }
}
