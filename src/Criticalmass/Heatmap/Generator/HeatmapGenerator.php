<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Generator;

use App\Criticalmass\Geo\Converter\TrackToPositionListConverter;
use App\Criticalmass\Heatmap\Canvas\Canvas;
use App\Criticalmass\Heatmap\Canvas\CanvasFactory;
use App\Criticalmass\Heatmap\CanvasCutter\CanvasCutter;
use App\Criticalmass\Heatmap\CoordCalculator\CoordCalculator;
use App\Criticalmass\Heatmap\DimensionCalculator\DimensionCalculator;
use App\Criticalmass\Heatmap\DimensionCalculator\HeatmapDimension;
use App\Criticalmass\Heatmap\HeatmapInterface;
use App\Criticalmass\Heatmap\Path\Path;
use App\Criticalmass\Heatmap\Path\PathList;
use App\Criticalmass\Heatmap\Path\PositionListToPathListConverter;
use App\Criticalmass\Heatmap\TilePrinter\TilePrinter;
use App\Criticalmass\Util\ClassUtil;
use App\Entity\Track;
use Symfony\Bridge\Doctrine\RegistryInterface;

class HeatmapGenerator
{
    /** @var HeatmapInterface $heatmap */
    protected $heatmap;

    /** @var TrackToPositionListConverter $trackToPositionListConverter */
    protected $trackToPositionListConverter;

    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var CanvasCutter $canvasCutter */
    protected $canvasCutter;

    /** @var CanvasFactory $canvasFactory */
    protected $canvasFactory;

    /** @var array $zoomLevels */
    protected $zoomLevels = [];

    public function __construct(RegistryInterface $registry, TrackToPositionListConverter $trackToPositionListConverter, CanvasCutter $canvasCutter, CanvasFactory $canvasFactory)
    {
        $this->registry = $registry;
        $this->trackToPositionListConverter = $trackToPositionListConverter;
        $this->canvasCutter = $canvasCutter;
        $this->canvasFactory = $canvasFactory;
    }

    public function setHeatmap(HeatmapInterface $heatmap): HeatmapGenerator
    {
        $this->heatmap = $heatmap;

        return $this;
    }

    public function setZoomLevels(array $zoomLevels): HeatmapGenerator
    {
        $this->zoomLevels = $zoomLevels;

        return $this;
    }

    public function generate(): HeatmapGenerator
    {
        $trackList = $this->collectUnpaintedTracks();

        /** @var Track $track */
        foreach ($trackList as $track) {
            try {
                $positionList = $this->trackToPositionListConverter->convert($track);
                $pathList = PositionListToPathListConverter::convert($positionList);
            } catch (\Exception $exception) {
                continue;
            }

            foreach ($this->zoomLevels as $zoomLevel) {
                $zoomLevel = (int) $zoomLevel;

                $heatmapDimension = DimensionCalculator::calculate($pathList, $zoomLevel);

                $canvas = $this->canvasFactory->create($heatmapDimension, $this->heatmap, $zoomLevel);

                $this->paintPathList($pathList, $canvas, $heatmapDimension);

                $this->canvasCutter->cutCanvas($this->heatmap, $canvas, $zoomLevel);
            }

            die;
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
                TilePrinter::printTile($tile, $path->getStartCoord());
            }
        }
    }
}
