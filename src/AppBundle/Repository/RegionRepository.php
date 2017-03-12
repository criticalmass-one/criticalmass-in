<?php

namespace Caldera\Bundle\CalderaBundle\Repository;

use Caldera\Bundle\CalderaBundle\Entity\Region;
use Doctrine\ORM\EntityRepository;

class RegionRepository extends EntityRepository
{
    public function findByParentRegion(Region $region)
    {
        $builder = $this->createQueryBuilder('region');

        $builder->select('region');

        $builder->where($builder->expr()->eq('region.parent', $region->getId()));

        $builder->addOrderBy('region.name', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function countChildren(Region $region)
    {
        $builder = $this->createQueryBuilder('region');

        $builder->select('COUNT(region)');

        $builder->where($builder->expr()->eq('region.parent', $region->getId()));

        $query = $builder->getQuery();

        return $query->getSingleScalarResult();
    }
}

