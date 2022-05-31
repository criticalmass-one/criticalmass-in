<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\TrackManager;

use App\Entity\Heatmap;
use App\Entity\Track;

interface TrackManagerInterface
{
    public function findUnpaintedTracksForHeatmap(Heatmap $heatmap): array;

    public function linkTrackToHeatmap(Track $track, Heatmap $heatmap): void;
}