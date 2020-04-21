<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\HeatmapTrackFactory;

use App\Entity\HeatmapTrack;

class HeatmapTrackFactory implements HeatmapTrackFactoryInterface
{
    /** @var HeatmapTrack $heatmapTrack */
    protected $heatmapTrack;

    public function __construct()
    {
        $this->heatmapTrack = new HeatmapTrack();
        $this->heatmapTrack->setCreatedAt(new \DateTime());
    }

    public function build(): HeatmapTrack
    {
        return $this->heatmapTrack;
    }
}
