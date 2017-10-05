<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Ride;
use Doctrine\ORM\EntityRepository;

class SubrideRepository extends EntityRepository
{
    public function getSubridesForRide(Ride $ride)
    {
        $builder = $this->createQueryBuilder('subride');

        $builder->select('subride');
        $builder->where($builder->expr()->eq('subride.ride', $ride->getId()));
        $builder->addOrderBy('subride.dateTime', 'ASC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}

