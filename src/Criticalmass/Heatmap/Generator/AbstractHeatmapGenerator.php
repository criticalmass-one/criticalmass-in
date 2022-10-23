<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Generator;

use App\Criticalmass\Geo\Converter\TrackToPositionListConverter;
use App\Criticalmass\Heatmap\Canvas\CanvasFactory;
use App\Criticalmass\Heatmap\CanvasCutter\CanvasCutter;
use App\Criticalmass\Heatmap\HeatmapInterface;
use App\Criticalmass\Heatmap\Status\StatusCallback;
use App\Criticalmass\Heatmap\TilePrinter\TilePrinter;
use App\Criticalmass\Heatmap\TrackManager\TrackManagerInterface;

abstract class AbstractHeatmapGenerator implements HeatmapGeneratorInterface
{
    /** @var HeatmapInterface $heatmap */
    protected $heatmap;

    /** @var int $paintedTracks */
    protected $paintedTracks = 0;

    /** @var int $maxPaintedTracks */
    protected $maxPaintedTracks = 0;

    /** @var StatusCallback $statusCallback */
    protected $statusCallback;

    public function __construct(protected TrackToPositionListConverter $trackToPositionListConverter, protected CanvasCutter $canvasCutter, protected CanvasFactory $canvasFactory, protected TilePrinter $tilePrinter, protected TrackManagerInterface $trackManager)
    {
    }

    public function setHeatmap(HeatmapInterface $heatmap): HeatmapGeneratorInterface
    {
        $this->heatmap = $heatmap;

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

    public function setStatusCallback(StatusCallback $statusCallback): HeatmapGeneratorInterface
    {
        $this->statusCallback = $statusCallback;

        return $this;
    }
}
