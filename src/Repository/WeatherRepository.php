<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Ride;
use App\Entity\Weather;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class WeatherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Weather::class);
    }

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

