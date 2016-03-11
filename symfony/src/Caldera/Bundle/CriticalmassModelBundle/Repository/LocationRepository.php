<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Doctrine\ORM\EntityRepository;

class LocationRepository extends EntityRepository
{
    public function findLocationsByCity(City $city)
    {
        $builder = $this->createQueryBuilder('location');

        $builder->select('location');

        $builder->orderBy('location.title', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }
}

