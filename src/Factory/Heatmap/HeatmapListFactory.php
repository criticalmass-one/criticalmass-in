<?php declare(strict_types=1);

namespace App\Factory\Heatmap;

use App\Entity\Heatmap;
use App\Entity\HeatmapTrack;
use App\Entity\Track;
use App\Model\Heatmap\HeatmapListModel;
use Doctrine\Persistence\ManagerRegistry;

class HeatmapListFactory implements HeatmapListFactoryInterface
{
    public function __construct(protected ManagerRegistry $registry)
    {
    }

    /**
     * TODO: Convert this into doctrine NEW() syntax
     */
    public function build(): array
    {
        $heatmapList = $this->registry->getRepository(Heatmap::class)->findAll();
        $modelList = [];

        /** @var Heatmap $heatmap */
        foreach ($heatmapList as $heatmap) {
            $heatmapTracks = $heatmap->getHeatmapTracks();
            $city = $heatmap->getCity();
            $trackCounter = $this->registry->getRepository(Track::class)->countByCity($city);
            $lastHeatmapTrack = $this->registry->getRepository(HeatmapTrack::class)->findLastHeatmapTrackForHeatmap($heatmap);

            $model = new HeatmapListModel(
                $city,
                $heatmap,
                count($heatmapTracks),
                $trackCounter,
                $lastHeatmapTrack ? $lastHeatmapTrack->getCreatedAt() : null
            );

            $modelList[] = $model;
        }

        usort($modelList, function (HeatmapListModel $hlm1, HeatmapListModel $hlm2) {
            $cityName1 = $hlm1->getCity()->getCity();
            $cityName2 = $hlm2->getCity()->getCity();

            if ($cityName1 === $cityName2) {
                return 0;
            }

            return $hlm1->getCity()->getCity() < $hlm2->getCity()->getCity() ? -1 : 1;
        });

        return $modelList;
    }
}