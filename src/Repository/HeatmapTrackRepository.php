<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Heatmap;
use App\Entity\HeatmapTrack;
use Doctrine\ORM\EntityRepository;
use function Doctrine\ORM\QueryBuilder;

class HeatmapTrackRepository extends EntityRepository
{
    public function findLastHeatmapTrackForHeatmap(Heatmap $heatmap): ?HeatmapTrack
    {
        $builder = $this->createQueryBuilder('ht');

        $builder
            ->where($builder->expr()->eq('ht.heatmap', ':heatmap'))
            ->setParameter('heatmap', $heatmap)
            ->orderBy('ht.createdAt', 'DESC')
            ->setMaxResults(1);

        $query = $builder->getQuery();

        return $query->getSingleResult();
    }
}
