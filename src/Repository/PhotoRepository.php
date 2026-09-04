<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Ride;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class PhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    /**
     * Je Tour, zu der die Person Fotos beigesteuert hat, die Tour und die Anzahl.
     *
     * Frueher stand in der Auswahl ein beliebiges Foto der Gruppe: MySQL sucht
     * sich dann stillschweigend eine Zeile aus, PostgreSQL lehnt die Abfrage ab,
     * weil die Spalten weder gruppiert noch aggregiert sind. Gebraucht wurde
     * ohnehin nur die Tour dahinter.
     *
     * Die Abfrage geht deshalb von Ride aus statt von Photo — eine verbundene
     * Kennung auszuwaehlen, ohne ihre Wurzel mitzunehmen, ist in DQL selbst
     * schon ein Fehler.
     *
     * @return list<array{0: Ride, 1: int}>
     */
    public function findRidesWithPhotoCounterByUser(User $user): array
    {
        $builder = $this->getEntityManager()->createQueryBuilder();

        $rows = $builder
            ->select('ride.id AS rideId')
            ->addSelect('COUNT(photo.id) AS photoCount')
            ->from(Ride::class, 'ride')
            ->innerJoin('ride.photos', 'photo')
            ->where($builder->expr()->eq('photo.deleted', ':deleted'))
            ->setParameter('deleted', false)
            ->andWhere($builder->expr()->eq('photo.user', ':user'))
            ->setParameter('user', $user)
            ->groupBy('ride.id')
            ->addGroupBy('ride.dateTime')
            ->orderBy('ride.dateTime', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->ridesForCountedRows($rows);
    }

    /**
     * Laedt die Touren zu einer gruppierten Zaehlung nach und behaelt deren
     * Reihenfolge bei. Die Stadt kommt mit, damit die Anzeige nicht je Zeile
     * nachfragen muss.
     *
     * @param list<array{rideId: int, photoCount: int|string}> $rows
     * @return list<array{0: Ride, 1: int}>
     */
    private function ridesForCountedRows(array $rows): array
    {
        if ($rows === []) {
            return [];
        }

        $rides = $this->getEntityManager()->createQueryBuilder()
            ->select('ride')
            ->addSelect('city')
            ->from(Ride::class, 'ride')
            ->innerJoin('ride.city', 'city')
            ->where('ride.id IN (:ids)')
            ->setParameter('ids', array_column($rows, 'rideId'))
            ->getQuery()
            ->getResult();

        $byId = [];

        foreach ($rides as $ride) {
            $byId[$ride->getId()] = $ride;
        }

        $result = [];

        foreach ($rows as $row) {
            $ride = $byId[$row['rideId']] ?? null;

            if ($ride instanceof Ride) {
                $result[] = [$ride, (int) $row['photoCount']];
            }
        }

        return $result;
    }

    public function findPhotosWithoutExifData(?int $limit = null, ?int $offset = null, bool $fetchExistingData = false): array
    {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('p')
            ->orderBy('p.exifCreationDate', 'desc');

        if (!$fetchExistingData) {
            $builder
                ->where($builder->expr()->isNull('p.exifExposure'))
                ->andWhere($builder->expr()->isNull('p.exifAperture'))
                ->andWhere($builder->expr()->isNull('p.exifIso'))
                ->andWhere($builder->expr()->isNull('p.exifFocalLength'))
                ->andWhere($builder->expr()->isNull('p.exifCamera'));
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        if ($offset) {
            $builder->setFirstResult($offset);
        }

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findPhotosWithoutExportData(?int $limit = null, ?int $offset = null, bool $fetchExistingData = false): array
    {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('p')
            ->orderBy('p.exifCreationDate', 'desc');

        if (!$fetchExistingData) {
            $builder->where($builder->expr()->isNull('p.imageGoogleCloudHash'));
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        if ($offset) {
            $builder->setFirstResult($offset);
        }

        $query = $builder->getQuery();

        return $query->getResult();
    }

    /**
     * Touren einer Stadt mit der Anzahl ihrer Fotos.
     *
     * Wie bei findRidesWithPhotoCounterByUser geht die Abfrage von Ride aus:
     * Vorher wurde ein beliebiges Foto der Gruppe ausgewaehlt, was PostgreSQL
     * ablehnt. Die Rueckgabeform bleibt unveraendert.
     *
     * @deprecated
     *
     * @return array{rides: array<string, Ride>, counter: array<string, int>}
     */
    public function findRidesWithPhotoCounter(?City $city = null): array
    {
        $builder = $this->getEntityManager()->createQueryBuilder();

        $builder
            ->select('ride.id AS rideId')
            ->addSelect('COUNT(photo.id) AS photoCount')
            ->from(Ride::class, 'ride')
            ->innerJoin('ride.photos', 'photo')
            ->where($builder->expr()->eq('photo.deleted', ':deleted'))
            ->setParameter('deleted', false);

        if ($city) {
            $builder
                ->andWhere($builder->expr()->eq('photo.city', ':city'))
                ->setParameter('city', $city);
        }

        $builder
            ->groupBy('ride.id')
            ->addGroupBy('ride.dateTime')
            ->orderBy('ride.dateTime', 'DESC');

        $rides = [];
        $counter = [];

        foreach ($this->ridesForCountedRows($builder->getQuery()->getResult()) as [$ride, $anzahl]) {
            $key = $ride->getDateTime()->format('Y-m-d') . '_' . $ride->getId();

            $rides[$key] = $ride;
            $counter[$key] = $anzahl;
        }

        return [
            'rides' => $rides,
            'counter' => $counter,
        ];
    }

    public function buildQueryPhotosByRide(Ride $ride): QueryBuilder
    {
        $builder = $this->createQueryBuilder('p');

        $builder->select('p')
            ->where($builder->expr()->eq('p.ride', ':ride'))
            ->setParameter('ride', $ride)
            ->andWhere($builder->expr()->eq('p.deleted', ':deleted'))
            ->setParameter('deleted', false)
            ->addOrderBy('p.exifCreationDate', 'ASC');

        return $builder;
    }

    public function findPhotosByRide(Ride $ride): array
    {
        $builder = $this->buildQueryPhotosByRide($ride);

        return $builder->getQuery()->getResult();
    }

    public function countPhotosByRide(Ride $ride): int
    {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('COUNT(p)')
            ->where($builder->expr()->eq('p.ride', ':ride'))
            ->setParameter('ride', $ride)
            ->andWhere($builder->expr()->eq('p.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($builder->expr()->eq('p.deleted', ':deleted'))
            ->setParameter('deleted', false);

        $query = $builder->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    public function buildQueryPhotosByUserAndRide(User $user, Ride $ride): Query
    {
        $builder = $this->createQueryBuilder('p');

        $builder->select('p')
        ->where($builder->expr()->eq('p.ride', ':ride'))
        ->setParameter('ride', $ride)
        ->andWhere($builder->expr()->eq('p.user', ':user'))
        ->setParameter('user', $user)
        ->andWhere($builder->expr()->eq('p.deleted', ':deleted'))
        ->setParameter('deleted', false)
        ->addOrderBy('p.exifCreationDate', 'ASC');

        return $builder->getQuery();
    }

    public function findPhotosByUserAndRide(User $user, Ride $ride): array
    {
        $query = $this->buildQueryPhotosByUserAndRide($user, $ride);

        return $query->getResult();
    }

    public function findSomePhotos(int $limit = 16, ?City $city = null): array
    {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('p')
            ->addSelect('RAND() as HIDDEN rand')
            ->where($builder->expr()->eq('p.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($builder->expr()->eq('p.deleted', ':deleted'))
            ->setParameter('deleted', false);

        if ($city) {
            $builder
                ->join('p.ride', 'r')
                ->andWhere($builder->expr()->eq('r.city', ':city'))
                ->setParameter('city', $city);
        }

        $builder
            ->addOrderBy('rand')
            ->setMaxResults($limit);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findForTimelineRidePhotoCollector(
        ?\DateTime $startDateTime = null,
        ?\DateTime $endDateTime = null,
        ?int $limit = null
    ) {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('p')
            ->where($builder->expr()->eq('p.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($builder->expr()->eq('p.deleted', ':deleted'))
            ->setParameter('deleted', false);

        if ($startDateTime) {
            $builder
                ->andWhere($builder->expr()->gte('p.exifCreationDate', ':startDateTime'))
                ->setParameter('startDateTime', $startDateTime);
        }

        if ($endDateTime) {
            $builder
                ->andWhere($builder->expr()->lte('p.exifCreationDate', ':endDateTime'))
                ->setParameter('endDateTime', $endDateTime);
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $builder->addOrderBy('p.exifCreationDate', 'DESC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function countByUser(User $user): int
    {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('COUNT(p)')
            ->where($builder->expr()->eq('p.user', ':user'))
            ->andWhere($builder->expr()->eq('p.enabled', ':enabled'))
            ->andWhere($builder->expr()->eq('p.deleted', ':deleted'))
            ->setParameter('user', $user)
            ->setParameter('enabled', true)
            ->setParameter('deleted', false);

        $query = $builder->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    public function findPhotosForExport(?int $limit = null, ?int $offset = null): array
    {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('p')
            ->orderBy('p.exifCreationDate', 'desc')
            ->where($builder->expr()->isNotNull('p.imageGoogleCloudHash'));

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        if ($offset) {
            $builder->setFirstResult($offset);
        }

        $query = $builder->getQuery();

        return $query->getResult();
    }
}

