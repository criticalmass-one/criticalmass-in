<?php

namespace AppBundle\Repository;

use AppBundle\Entity\City;
use AppBundle\Entity\Location;
use AppBundle\Entity\Ride;
use Doctrine\ORM\EntityRepository;

class LocationRepository extends EntityRepository
{
    public function findLocationsByCity(City $city): array
    {
        $builder = $this->createQueryBuilder('l');

        $builder
            ->select('l')
            ->where($builder->expr()->eq('l.city', ':city'))
            ->orderBy('l.title', 'ASC')
            ->setParameter('city', $city);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findLocationForRide(Ride $ride): ?Location
    {
        if (!$ride->getHasLocation()) {
            return null;
        }

        $builder = $this->createQueryBuilder('l');

        $builder
            ->where($builder->expr()->like('l.title', ':locationTitle'))
            ->andWhere($builder->expr()->eq('l.city', ':city'))
            ->setParameter('locationTitle', $ride->getLocation())
            ->setParameter('city', $ride->getCity())
            ->setMaxResults(1);

        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }
}

