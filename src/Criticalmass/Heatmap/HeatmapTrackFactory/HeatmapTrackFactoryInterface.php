<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\HeatmapTrackFactory;

use App\Entity\HeatmapTrack;

interface HeatmapTrackFactoryInterface
{
    public function build(): HeatmapTrack;
}
