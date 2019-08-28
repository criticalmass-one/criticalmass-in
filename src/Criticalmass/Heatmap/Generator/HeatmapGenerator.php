<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Generator;

use App\Criticalmass\Heatmap\Canvas\Canvas;
use App\Criticalmass\Heatmap\CoordCalculator\CoordCalculator;
use App\Criticalmass\Heatmap\DimensionCalculator\DimensionCalculator;
use App\Criticalmass\Heatmap\DimensionCalculator\HeatmapDimension;
use App\Criticalmass\Heatmap\Path\Path;
use App\Criticalmass\Heatmap\Path\PathList;
use App\Criticalmass\Heatmap\Path\PositionListToPathListConverter;
use App\Criticalmass\Heatmap\Status\Status;
use App\Entity\Track;

class HeatmapGenerator extends AbstractHeatmapGenerator
{
    const MIN_ZOOMLEVEL = 10;
    const MAX_ZOOMLEVEL = 16;

    public function generate(): HeatmapGeneratorInterface
    {
        $trackList = $this->trackManager->findUnpaintedTracksForHeatmap($this->heatmap);

        $status = new Status(count($trackList));

        /** @var Track $track */
        foreach ($trackList as $track) {
            if ($this->paintedTracks === $this->maxPaintedTracks) {
                break;
            }

            try {
                $positionList = $this->trackToPositionListConverter->convert($track);
                $pathList = PositionListToPathListConverter::convert($positionList);
            } catch (\Exception $exception) {
                continue;
            }

            for ($zoomLevel = self::MIN_ZOOMLEVEL; $zoomLevel <= self::MAX_ZOOMLEVEL; ++$zoomLevel) {
                $heatmapDimension = DimensionCalculator::calculate($pathList, $zoomLevel);

                $status = $status->setZoomLevel($zoomLevel)
                    ->resetPaintedTiles()->setMaxTiles($heatmapDimension->getWidth() * $heatmapDimension->getHeight())
                    ->setMemoryUsage(memory_get_usage(true));

                $this->statusCallback->onZoomLevel($status);

                $canvas = $this->canvasFactory->create($heatmapDimension, $this->heatmap, $zoomLevel);

                $this->paintPathList($pathList, $canvas, $heatmapDimension);

                $this->canvasCutter->cutCanvas($this->heatmap, $canvas, $zoomLevel);

                unset($canvas);
            }

            $this->trackManager->linkTrackToHeatmap($track, $this->heatmap);

            ++$this->paintedTracks;

            $status->incPaintedTracks();
        }

        return $this;
    }

    protected function paintPathList(PathList $pathList, Canvas $canvas, HeatmapDimension $heatmapDimension): void
    {
        /** @var Path $path */
        foreach ($pathList as $path) {
            if (!$path) {
                break;
            }

            $vectorX = $path->getEndCoord()->getLongitude() - $path->getStartCoord()->getLongitude();
            $vectorY = $path->getEndCoord()->getLatitude() - $path->getStartCoord()->getLatitude();

            $partsPerPath = (int)floor($heatmapDimension->getZoomLevel() / 2);

            for ($part = 1; $part <= $partsPerPath; ++$part) {
                $longitude = $path->getStartCoord()->getLongitude() + (float)$vectorX / $partsPerPath * $part;
                $latitude = $path->getStartCoord()->getLatitude() + (float)$vectorY / $partsPerPath * $part;

                $tileX = CoordCalculator::longitudeToXTile($longitude, $heatmapDimension->getZoomLevel());
                $tileY = CoordCalculator::latitudeToYTile($latitude, $heatmapDimension->getZoomLevel());

                if ($tile = $canvas->getTile($tileX, $tileY)) {
                    $this->tilePrinter->printTile($tile, $path->getStartCoord());
                }
            }
        }
    }
}
