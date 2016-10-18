<?php

namespace Caldera\Bundle\CalderaBundle\Repository;

use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Doctrine\ORM\EntityRepository;

class FacebookCityPropertiesRepository extends EntityRepository
{
    public function findByRide(Ride $ride)
    {
        $builder = $this->createQueryBuilder('frp');

        $builder->select('frp');

        $builder->where($builder->expr()->eq('frp.ride', $ride->getId()));

        $builder->orderBy('frp.createdAt');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findForStatistic(): array
    {
        $builder = $this->createQueryBuilder('frp');

        $builder->select('frp');

        $builder->where($builder->expr()->eq('DAYOFWEEK(frp.createdAt)', 1));

        $builder->orderBy('frp.createdAt');

        $query = $builder->getQuery();

        return $query->getResult();
    }
}

