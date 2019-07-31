<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Generator;

use App\Criticalmass\Geo\Converter\TrackToPositionListConverter;
use App\Criticalmass\Geo\EntityInterface\TrackInterface;
use App\Criticalmass\Heatmap\Brush\Brush;
use App\Criticalmass\Heatmap\Brush\Pencil;
use App\Criticalmass\Heatmap\Canvas\Canvas;
use App\Criticalmass\Heatmap\CoordCalculator\CoordCalculator;
use App\Criticalmass\Heatmap\DimensionCalculator\DimensionCalculator;
use App\Criticalmass\Heatmap\HeatmapInterface;
use App\Criticalmass\Heatmap\Path\Path;
use App\Criticalmass\Heatmap\Path\PositionListToPathListConverter;
use App\Criticalmass\Heatmap\Tile\Tile;
use App\Criticalmass\Util\ClassUtil;
use App\Entity\Track;
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

    public function __construct(RegistryInterface $registry, TrackToPositionListConverter $trackToPositionListConverter)
    {
        $this->registry = $registry;
        $this->trackToPositionListConverter = $trackToPositionListConverter;
    }

    public function setHeatmap(HeatmapInterface $heatmap): HeatmapGenerator
    {
        $this->heatmap = $heatmap;

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

            $heatmapDimension = DimensionCalculator::calculate($pathList, 15);

            $canvas = Canvas::fromHeatmapDimension($heatmapDimension);

            /** @var Path $path */
            foreach ($pathList as $path) {
                if (!$path) {
                    break;
                }

                $vector[0] = (float) $path->getEndCoord()->getLatitude() - $path->getStartCoord()->getLatitude();
                $vector[1] = (float) $path->getEndCoord()->getLongitude() - $path->getStartCoord()->getLongitude();

                $n = 25;
                for ($i = 0; $i < $n; ++$i)
                {
                    $latitude = (float) $path->getStartCoord()->getLatitude() + (float) $i * $vector[0] * (1 / $n);
                    $longitude = (float) $path->getStartCoord()->getLongitude() + (float) $i * $vector[1] * (1 / $n);
                    $x = (float) $canvas->getWidth() * Tile::SIZE / ($heatmapDimension->getRightLongitude() - $heatmapDimension->getLeftLongitude()) * ($longitude - $heatmapDimension->getLeftLongitude());
                    $y = (float) $canvas->getHeight() * Tile::SIZE / ($heatmapDimension->getBottomLatitude() - $heatmapDimension->getTopLatitude()) * ($latitude - $heatmapDimension->getTopLatitude());

                    $point = new Point((int) round($x), (int) round($y));
                    $color = (new RGBPalette())->color('#FF0000');

                    Brush::paint($canvas, $point, $color);
                }
            }


            header('Content-type: image/png');
            echo $canvas->image()->get('png');
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
}