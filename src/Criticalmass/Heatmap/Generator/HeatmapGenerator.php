<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Generator;

use App\Criticalmass\Heatmap\Canvas\Canvas;
use App\Criticalmass\Heatmap\CoordCalculator\CoordCalculator;
use App\Criticalmass\Heatmap\DimensionCalculator\DimensionCalculator;
use App\Criticalmass\Heatmap\DimensionCalculator\HeatmapDimension;
use App\Criticalmass\Heatmap\Path\Path;
use App\Criticalmass\Heatmap\Path\PathList;
use App\Criticalmass\Heatmap\Path\PositionListToPathListConverter;
use App\Criticalmass\Heatmap\TilePrinter\TilePrinter;
use App\Criticalmass\Util\ClassUtil;
use App\Entity\Track;

class HeatmapGenerator extends AbstractHeatmapGenerator
{
    /** @var Status $status */
    protected $status;

    public function generate(): HeatmapGeneratorInterface
    {
        $manager = $this->registry->getManager();

        $trackList = $this->collectUnpaintedTracks();

        $this->status = new Status(count($trackList));

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

            foreach ($this->zoomLevels as $zoomLevel) {
                $zoomLevel = (int) $zoomLevel;

                $heatmapDimension = DimensionCalculator::calculate($pathList, $zoomLevel);

                $this->status
                    ->setZoomLevel($zoomLevel)
                    ->resetPaintedTiles()->setMaxTiles($heatmapDimension->getWidth() * $heatmapDimension->getHeight());

                call_user_func($this->callback, $this->status);

                $canvas = $this->canvasFactory->create($heatmapDimension, $this->heatmap, $zoomLevel);

                $this->paintPathList($pathList, $canvas, $heatmapDimension);

                $this->canvasCutter->cutCanvas($this->heatmap, $canvas, $zoomLevel);
            }

            $track->addHeatmap($this->heatmap);

            ++$this->paintedTracks;
            $this->status->incPaintedTracks();
        }

        $manager->flush();

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
                TilePrinter::printTile($tile, $path->getStartCoord());
            }
        }
    }
}
