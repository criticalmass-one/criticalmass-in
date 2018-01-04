<?php

namespace Criticalmass\Bundle\AppBundle\Repository;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\Location;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
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
            ->setParameter('city', $city)
        ;

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
            ->setMaxResults(1)
        ;

        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }
}

