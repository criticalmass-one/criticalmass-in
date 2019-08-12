<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Generator;

use App\Criticalmass\Geo\Converter\TrackToPositionListConverter;
use App\Criticalmass\Heatmap\Brush\Brush;
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
use App\Criticalmass\Heatmap\Pipette\Pipette;
use App\Criticalmass\Heatmap\Tile\Tile;
use App\Criticalmass\Util\ClassUtil;
use App\Entity\Track;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB as RGBPalette;
use Imagine\Image\Point;
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

            $latitude = $path->getStartCoord()->getLatitude();
            $longitude = $path->getStartCoord()->getLongitude();

            $tileX = CoordCalculator::longitudeToXTile($longitude, $heatmapDimension->getZoomLevel());
            $tileY = CoordCalculator::latitudeToYTile($latitude, $heatmapDimension->getZoomLevel());

            $tileTopLatitude = CoordCalculator::yTileToLatitude($tileY, $heatmapDimension->getZoomLevel());
            $tileLeftLongitude = CoordCalculator::xTileToLongitude($tileX, $heatmapDimension->getZoomLevel());
            $tileBottomLatitude = CoordCalculator::yTileToLatitude($tileY + 1, $heatmapDimension->getZoomLevel());
            $tileRightLongitude = CoordCalculator::xTileToLongitude($tileX + 1, $heatmapDimension->getZoomLevel());
                        $y = Tile::SIZE * ($tileTopLatitude - $path->getStartCoord()->getLatitude()) / ($tileTopLatitude - $tileBottomLatitude);

                      $x = Tile::SIZE * ($path->getStartCoord()->getLongitude() - $tileLeftLongitude) / ($tileRightLongitude - $tileLeftLongitude);

            $point = new Point($x, $y);
            $canvas->getTile($tileX, $tileY)->image()->draw()->ellipse($point, new Box(2, 2), (new RGBPalette())->color('#FF0000'));

//            $y = 512 * ($heatmapDimension->getTopLatitude() - $path->getStartCoord()->getLatitude()) / ($heatmapDimension->getTopLatitude() - $heatmapDimension->getBottomLatitude());

  //          $x = 512 * ($path->getStartCoord()->getLongitude() - $heatmapDimension->getLeftLongitude()) / ($heatmapDimension->getRightLongitude() - $heatmapDimension->getLeftLongitude());

            //$this->draw($canvas, (int) round($x), (int) round($y));
        }

    }

    protected function draw(Canvas $canvas, int $x, int $y): void
    {
        try {
            $point = new Point((int)round($x), (int)round($y));

            $white = (new RGBPalette())->color('#FFFFFF');
            $red = (new RGBPalette())->color('#FF0000');
            $blue = (new RGBPalette())->color('#0000FF');

            try {
                $oldColor = Pipette::getColor($canvas, $point);

                if ($oldColor !== $white) {
                    Brush::paint($canvas, $point, $red);
                } else {
                    Brush::paint($canvas, $point, $blue);
                }
            } catch (\RuntimeException $exception) {
                //Brush::paint($canvas, $point, $blue);
            }
        } catch (\InvalidArgumentException $exception) {

        }
    }
}