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

    /** @var TrackToPositionListConverter $trackToPositionListConverter */
    protected $trackToPositionListConverter;

    /** @var CanvasCutter $canvasCutter */
    protected $canvasCutter;

    /** @var CanvasFactory $canvasFactory */
    protected $canvasFactory;

    /** @var int $paintedTracks */
    protected $paintedTracks = 0;

    /** @var int $maxPaintedTracks */
    protected $maxPaintedTracks = 0;

    /** @var StatusCallback $statusCallback */
    protected $statusCallback;

    /** @var TilePrinter $tilePrinter */
    protected $tilePrinter;

    /** @var TrackManagerInterface $trackManager */
    protected $trackManager;

    public function __construct(TrackToPositionListConverter $trackToPositionListConverter, CanvasCutter $canvasCutter, CanvasFactory $canvasFactory, TilePrinter $tilePrinter, TrackManagerInterface $trackManager)
    {
        $this->trackToPositionListConverter = $trackToPositionListConverter;
        $this->canvasCutter = $canvasCutter;
        $this->canvasFactory = $canvasFactory;
        $this->tilePrinter = $tilePrinter;
        $this->trackManager = $trackManager;
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
