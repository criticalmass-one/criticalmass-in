<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Generator;

use App\Criticalmass\Geo\Converter\TrackToPositionListConverter;
use App\Criticalmass\Geo\EntityInterface\TrackInterface;
use App\Criticalmass\Heatmap\HeatmapInterface;
use App\Criticalmass\Heatmap\Path\PositionListToPathListConverter;

class HeatmapGenerator
{
    /** @var HeatmapInterface $heatmap */
    protected $heatmap;

    /** @var  */
    protected $trackToPositionListConverter;

    public function __construct(TrackToPositionListConverter $trackToPositionListConverter)
    {
        $this->trackToPositionListConverter = $trackToPositionListConverter;
    }

    public function setHeatmap(HeatmapInterface $heatmap): HeatmapGenerator
    {
        $this->heatmap = $heatmap;

        return $this;
    }

    public function addTrack(TrackInterface $track)
    {
        $positionList = $this->trackToPositionListConverter->convert($track);
        $pathList = PositionListToPathListConverter::convert($positionList);

        
    }
}