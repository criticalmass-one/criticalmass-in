<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Region;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RegionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Region::class);
    }

    public function findByParentRegion(Region $region): array
    {
        $builder = $this->createQueryBuilder('r');

        $builder
            ->select('r')
            ->where($builder->expr()->eq('r.parent', ':region'))
            ->setParameter('region', $region)
            ->addOrderBy('r.name', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function countChildren(Region $region): int
    {
        $builder = $this->createQueryBuilder('r');

        $builder
            ->select('COUNT(r)')
            ->where($builder->expr()->eq('r.parent', ':region'))
            ->setParameter('region', $region);

        $query = $builder->getQuery();

        return (int) $query->getSingleScalarResult();
    }
}

