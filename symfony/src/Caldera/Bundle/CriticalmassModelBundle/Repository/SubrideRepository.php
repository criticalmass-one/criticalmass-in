<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Doctrine\ORM\EntityRepository;

class SubrideRepository extends EntityRepository
{
    public function getSubridesForRide(Ride $ride)
    {
        $builder = $this->createQueryBuilder('subride');

        $builder->select('subride');
        $builder->where($builder->expr()->eq('subride.ride', $ride->getId()));
        $builder->where($builder->expr()->eq('subride.isArchived', 0));
        $builder->addOrderBy('subride.dateTime', 'ASC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}

