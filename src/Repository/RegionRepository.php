<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Region;
use Doctrine\ORM\EntityRepository;

class RegionRepository extends EntityRepository
{
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

    public function countChildren(Region $region)
    {
        $builder = $this->createQueryBuilder('region');

        $builder->select('COUNT(region)');

        $builder->where($builder->expr()->eq('region.parent', $region->getId()));

        $query = $builder->getQuery();

        return (int) $query->getSingleScalarResult();
    }
}

