<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\CanvasCutter;

use App\Criticalmass\Heatmap\Canvas\Canvas;
use App\Criticalmass\Heatmap\CoordCalculator\CoordCalculator;
use App\Criticalmass\Heatmap\HeatmapInterface;
use App\Criticalmass\Heatmap\Tile\TilePersister;

class CanvasCutter
{
    /** @var TilePersister $tilePersister */
    protected $tilePersister;

    public function __construct(TilePersister $tilePersister)
    {
        $this->tilePersister = $tilePersister;
    }

    public function cutCanvas(HeatmapInterface $heatmap, Canvas $canvas, int $zoomLevel): CanvasCutter
    {
        $startX = CoordCalculator::longitudeToXTile($canvas->getTopLeftCoord()->getLongitude(), $zoomLevel);
        $startY = CoordCalculator::latitudeToYTile($canvas->getTopLeftCoord()->getLatitude(), $zoomLevel);

        for ($x = $startX; $x < $startX + $canvas->getWidth(); ++$x) {
            for ($y = $startY; $y < $startY + $canvas->getHeight(); ++$y) {
                $tile = $canvas->getTile($x, $y);

                if ($tile) {
                    $this->tilePersister->save($heatmap, $tile);
                }
            }
        }

        return $this;
    }
}
