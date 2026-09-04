<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\City;
use App\Entity\Location;
use App\Entity\Ride;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

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
        if (!$ride->getLatitude() || !$ride->getLongitude()) {
            return null;
        }

        $builder = $this->createQueryBuilder('l');

        // LOWER() auf beiden Seiten, weil PostgreSQL LIKE schreibungsempfindlich
        // vergleicht. Hier stehen keine Platzhalter im Muster, der Vergleich ist
        // also faktisch eine Gleichheit — enthaelt der Ortsname eines Rides ein
        // % oder _, wirkt es allerdings als Platzhalter. Das ist ein eigener,
        // aelterer Fehler und bleibt hier unangetastet.
        $builder
            ->where($builder->expr()->like('LOWER(l.title)', ':locationTitle'))
            ->andWhere($builder->expr()->eq('l.city', ':city'))
            ->setParameter('locationTitle', mb_strtolower((string) $ride->getLocation()))
            ->setParameter('city', $ride->getCity())
            ->setMaxResults(1);

        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }
}

