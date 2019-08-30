<?php declare(strict_types=1);

namespace App\Factory\Heatmap;

use App\Entity\Heatmap;
use App\Entity\HeatmapTrack;
use App\Model\Heatmap\UserHeatmapTrackModel;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserHeatmapTrackFactory implements UserHeatmapTrackFactoryInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function generateList(Heatmap $heatmap)
    {
        $list = [];

        $heatmapTrackList = $this->registry->getRepository(HeatmapTrack::class)->findByHeatmap($heatmap);

        /** @var HeatmapTrack $heatmapTrack */
        foreach ($heatmapTrackList as $heatmapTrack) {
            $track = $heatmapTrack->getTrack();
            $user = $track->getUser();

            if (array_key_exists($user->getId(), $list)) {
                $list[$user->getId()]->addTrack($track);
            } else {
                $model = new UserHeatmapTrackModel($user, $heatmap, [$track]);

                $list[$user->getId()] = $model;
            }
        }

        return $list;
    }
}
