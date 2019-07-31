<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Canvas;

use App\Criticalmass\Heatmap\CoordCalculator\CoordCalculator;
use App\Criticalmass\Heatmap\DimensionCalculator\HeatmapDimension;
use App\Criticalmass\Heatmap\Tile\Tile;
use Caldera\GeoBasic\Coord\Coord;

class CanvasFactory
{
    public function createFromHeatmapDimension(HeatmapDimension $heatmapDimension, int $zoomLevel): Canvas
    {
        $topLeftCoord = new Coord(
            CoordCalculator::yTileToLatitude($heatmapDimension->getTopTile(), $zoomLevel),
            CoordCalculator::xTileToLongitude($heatmapDimension->getLeftTile(), $zoomLevel)
        );

        $canvas = new Canvas($heatmapDimension->getWidth(), $heatmapDimension->getHeight(), $topLeftCoord);

        for ($x = $heatmapDimension->getLeftTile(); $x < $heatmapDimension->getWidth(); ++$x) {
            for ($y = $heatmapDimension->getTopTile(); $y < $heatmapDimension->getHeight(); ++$y) {
                $canvas->setTile($x, $y, new Tile($x, $y, $zoomLevel));
            }
        }

        return $canvas;
    }
}
