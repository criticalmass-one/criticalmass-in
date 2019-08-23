<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\TrackManager;

use App\Criticalmass\Heatmap\HeatmapTrackFactory\HeatmapTrackFactoryInterface;
use App\Entity\Heatmap;
use App\Entity\Track;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TrackManager implements TrackManagerInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var HeatmapTrackFactoryInterface $heatmapTrackFactory */
    protected $heatmapTrackFactory;

    public function __construct(RegistryInterface $registry, HeatmapTrackFactoryInterface $heatmapTrackFactory)
    {
        $this->registry = $registry;
        $this->heatmapTrackFactory = $heatmapTrackFactory;
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
