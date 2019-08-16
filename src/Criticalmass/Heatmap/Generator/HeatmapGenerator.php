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
use App\Criticalmass\Util\ClassUtil;
use App\Entity\Track;

class HeatmapGenerator extends AbstractHeatmapGenerator
{
    const MIN_ZOOMLEVEL = 5;
    const MAX_ZOOMLEVEL = 18;

    public function generate(): HeatmapGeneratorInterface
    {
        $manager = $this->registry->getManager();

        $trackList = $this->collectUnpaintedTracks();

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

            $track->addHeatmap($this->heatmap);
            $manager->flush();

            ++$this->paintedTracks;

            $status->incPaintedTracks();
        }

        return $this;
    }

    protected function collectUnpaintedTracks(): array
    {
        $parentEntity = $this->heatmap->getUser() ?? $this->heatmap->getCity() ?? $this->heatmap->getRide();

        $className = ClassUtil::getShortname($parentEntity);

        $repositoryMethod = sprintf('findBy%s', $className);

        $trackList = $this->registry->getRepository(Track::class)->$repositoryMethod($parentEntity);

        /** TODO move this check into repository */
        /** @var Track $track */
        foreach ($trackList as $key => $track) {
            if ($track->getHeatmaps()->contains($this->heatmap)) {
                unset($trackList[$key]);
            }
        }

        return $trackList;
    }

    protected function paintPathList(PathList $pathList, Canvas $canvas, HeatmapDimension $heatmapDimension): void
    {
        /** @var Path $path */
        foreach ($pathList as $path) {
            if (!$path) {
                break;
            }

            $tileX = CoordCalculator::longitudeToXTile($path->getStartCoord()->getLongitude(), $heatmapDimension->getZoomLevel());
            $tileY = CoordCalculator::latitudeToYTile($path->getStartCoord()->getLatitude(), $heatmapDimension->getZoomLevel());

            if ($tile = $canvas->getTile($tileX, $tileY)) {
                $this->tilePrinter->printTile($tile, $path->getStartCoord());
            }
        }
    }
}
