<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\TrackManager;

use App\Criticalmass\Heatmap\HeatmapTrackFactory\HeatmapTrackFactoryInterface;
use App\Entity\Heatmap;
use App\Entity\Track;
use Doctrine\Persistence\ManagerRegistry;

class TrackManager implements TrackManagerInterface
{
    public function __construct(protected ManagerRegistry $registry, protected HeatmapTrackFactoryInterface $heatmapTrackFactory)
    {
    }

    public function findUnpaintedTracksForHeatmap(Heatmap $heatmap): array
    {
        return $this->registry->getRepository(Track::class)->findUnpaintedTracksForHeatmap($heatmap, null);
    }

    public function linkTrackToHeatmap(Track $track, Heatmap $heatmap): void
    {
        $heatmapTrack = $this->heatmapTrackFactory->build();

        $heatmapTrack
            ->setTrack($track)
            ->setHeatmap($heatmap);

        $manager = $this->registry->getManager();
        $manager->persist($heatmapTrack);
        $manager->flush();
    }
}
