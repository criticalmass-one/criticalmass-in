<?php declare(strict_types=1);

namespace App\Factory\Heatmap;

use App\Entity\Heatmap;
use App\Model\Heatmap\HeatmapListModel;
use Symfony\Bridge\Doctrine\RegistryInterface;

class HeatmapListFactory implements HeatmapListFactoryInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
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

            $model = new HeatmapListModel(
                $heatmap->getCity(),
                $heatmap,
                count($heatmapTracks),
                count($heatmapTracks) > 0 ? $heatmapTracks->last()->getCreatedAt() : null
            );

            $modelList[] = $model;
        }

        return $modelList;
    }
}