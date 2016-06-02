<?php

namespace Caldera\Bundle\CalderaBundle\Repository;

use Application\Sonata\UserBundle\Entity\User;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\Track;
use Doctrine\ORM\EntityRepository;

/**
 * @package Caldera\Bundle\CalderaBundle\Repository
 * @author maltehuebner
 * @since 2016-02-16
 */
class WeatherRepository extends EntityRepository
{
    public function findCurrentWeatherForRide(Ride $ride)
    {
        $builder = $this->createQueryBuilder('weather');

        $builder->select('weather');
        $builder->where($builder->expr()->eq('weather.ride', $ride->getId()));

        $builder->orderBy('weather.creationDateTime', 'DESC');
        $builder->setMaxResults(1);

        $query = $builder->getQuery();

        $result = $query->getOneOrNullResult();

        return $result;
    }
}

