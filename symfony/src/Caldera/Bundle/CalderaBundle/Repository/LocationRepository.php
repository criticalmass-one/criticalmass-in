<?php

namespace Caldera\Bundle\CalderaBundle\Repository;

use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Doctrine\ORM\EntityRepository;

class LocationRepository extends EntityRepository
{
    public function findLocationsByCity(City $city)
    {
        $builder = $this->createQueryBuilder('location');

        $builder->select('location');

        $builder->where($builder->expr()->eq('location.city', $city->getId()));

        $builder->orderBy('location.title', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findLocationForRide(Ride $ride)
    {
        if (!$ride->getHasLocation()) {
            return null;
        }

        $builder = $this->createQueryBuilder('location');

        $builder->select('location');

        $builder->where($builder->expr()->like('location.title', '\'%' . $ride->getLocation() . '%\''));
        $builder->andWhere($builder->expr()->eq('location.city', $ride->getCity()->getId()));

        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }
}

