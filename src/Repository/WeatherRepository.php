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
        if (null === $ride->getDateTime()) {
            return null;
        }

        // Der Tourtag in Ortszeit; weatherDateTime traegt den Vorhersagetag als UTC-Datum.
        $zeitzone = new \DateTimeZone($ride->getCity()?->getTimezone() ?: 'UTC');
        $tourtag = \DateTimeImmutable::createFromMutable($ride->getDateTime())->setTimezone($zeitzone)->format('Y-m-d');
        $tagesbeginn = new \DateTime($tourtag, new \DateTimeZone('UTC'));

        $builder = $this->createQueryBuilder('w');

        $builder
            ->select('w')
            ->where($builder->expr()->eq('w.ride', ':ride'))
            ->andWhere($builder->expr()->gte('w.weatherDateTime', ':tagesbeginn'))
            ->andWhere($builder->expr()->lt('w.weatherDateTime', ':tagesende'))
            ->orderBy('w.creationDateTime', 'DESC')
            ->setMaxResults(1)
            ->setParameter('ride', $ride)
            ->setParameter('tagesbeginn', $tagesbeginn)
            ->setParameter('tagesende', (clone $tagesbeginn)->modify('+1 day'));

        $query = $builder->getQuery();

        $result = $query->getOneOrNullResult();

        return $result;
    }
}

