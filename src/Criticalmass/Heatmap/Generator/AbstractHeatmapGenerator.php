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

abstract class AbstractHeatmapGenerator implements HeatmapGeneratorInterface
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

    /** @var int $paintedTracks */
    protected $paintedTracks = 0;

    /** @var int $maxPaintedTracks */
    protected $maxPaintedTracks = 0;

    public function __construct(RegistryInterface $registry, TrackToPositionListConverter $trackToPositionListConverter, CanvasCutter $canvasCutter, CanvasFactory $canvasFactory)
    {
        $this->registry = $registry;
        $this->trackToPositionListConverter = $trackToPositionListConverter;
        $this->canvasCutter = $canvasCutter;
        $this->canvasFactory = $canvasFactory;
    }

    public function setHeatmap(HeatmapInterface $heatmap): HeatmapGeneratorInterface
    {
        $this->heatmap = $heatmap;

        return $this;
    }

    public function setZoomLevels(array $zoomLevels): HeatmapGeneratorInterface
    {
        $this->zoomLevels = $zoomLevels;

        return $this;
    }

    public function setPaintedTracks(int $paintedTracks): HeatmapGeneratorInterface
    {
        $this->paintedTracks = $paintedTracks;
        
        return $this;
    }

    public function setMaxPaintedTracks(int $maxPaintedTracks): HeatmapGeneratorInterface
    {
        $this->maxPaintedTracks = $maxPaintedTracks;

        return $this;
    }
}
