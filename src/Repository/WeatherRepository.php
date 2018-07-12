<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Ride;
use AppBundle\Entity\Weather;
use Doctrine\ORM\EntityRepository;

class WeatherRepository extends EntityRepository
{
    public function findCurrentWeatherForRide(Ride $ride): ?Weather
    {
        $builder = $this->createQueryBuilder('w');

        $builder
            ->select('w')
            ->where($builder->expr()->eq('w.ride', ':ride'))
            ->orderBy('w.creationDateTime', 'DESC')
            ->setMaxResults(1)
            ->setParameter('ride', $ride);

        $query = $builder->getQuery();

        $result = $query->getOneOrNullResult();

        return $result;
    }
}

