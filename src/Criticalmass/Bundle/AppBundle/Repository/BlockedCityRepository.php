<?php

namespace Criticalmass\Bundle\AppBundle\Repository;

use AppBundle\Entity\City;
use Doctrine\ORM\EntityRepository;

class BlockedCityRepository extends EntityRepository
{
    public function findCurrentCityBlock(City $city)
    {
        $builder = $this->createQueryBuilder('cb');

        $builder->select('cb');

        $builder->where($builder->expr()->eq('cb.city', $city->getId()));

        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }

}

