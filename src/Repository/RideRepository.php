<?php declare(strict_types=1);

namespace App\Repository;

use App\Criticalmass\Util\DateTimeUtil;
use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Location;
use App\Entity\Region;
use App\Entity\Ride;
use App\Entity\CitySlug;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

class RideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ride::class);
    }

    public function findCurrentRideForCity(City $city, bool $cycleMandatory = false, bool $slugsAllowed = true): ?Ride
    {
        $dateTime = \DateTime::createFromFormat('U', (string)time()); // this will allow to mock the clock in functional tests

        $builder = $this->createQueryBuilder('r');

        $builder
            ->select('r')
            ->where($builder->expr()->gte('r.dateTime', ':dateTime'))
            ->andWhere($builder->expr()->eq('r.city', ':city'))
            ->addOrderBy('r.dateTime', 'ASC')
            ->setParameter('dateTime', $dateTime)
            ->setParameter('city', $city);

        if ($cycleMandatory === true) {
            $builder->andWhere($builder->expr()->isNotNull('r.cycle'));
        }

        if ($slugsAllowed === false) {
            $builder->andWhere($builder->expr()->isNull('r.slug'));
        }

        $query = $builder->getQuery();
        $query->setMaxResults(1);

        $result = $query->getOneOrNullResult();

        return $result;
    }

    public function findRidesForCity(City $city, string $order = 'DESC', ?int $maxResults = null): array
    {
        $builder = $this->createQueryBuilder('ride');

        $builder
            ->select('ride')
            ->where($builder->expr()->eq('ride.city', $city->getId()))
            ->addOrderBy('ride.dateTime', $order);

        if ($maxResults) {
            $builder
                ->setMaxResults($maxResults);
        }

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function findRecentRides(
        $year = null,
        $month = null,
        $maxResults = null,
        $minParticipants = 0,
        $postShuffle = false
    ): array {
        $builder = $this->createQueryBuilder('ride');

        $builder->select('ride');

        if ($minParticipants) {
            $builder->where($builder->expr()->gte('ride.estimatedParticipants', $minParticipants));
        }

        if ($month && $year) {
            $builder->andWhere($builder->expr()->eq('MONTH(ride.dateTime)', $month));
            $builder->andWhere($builder->expr()->eq('YEAR(ride.dateTime)', $year));
        }

        $builder->addOrderBy('ride.dateTime', 'DESC');

        $query = $builder->getQuery();

        if ($maxResults) {
            $query->setMaxResults($maxResults);
        }

        $result = $query->getResult();

        if ($postShuffle) {
            $result = array_rand($result);
        }

        return $result;
    }

    public function findCurrentRides($order = 'ASC'): array
    {
        $startDateTime = new \DateTime();
        $startDateTimeInterval = new \DateInterval('P4W'); // four weeks ago
        $startDateTime->add($startDateTimeInterval);

        $endDateTime = new \DateTime();
        $endDateTimeInterval = new \DateInterval('P1W'); // one week after
        $endDateTime->sub($endDateTimeInterval);

        $builder = $this->createQueryBuilder('r');

        $builder
            ->select('r')
            ->where($builder->expr()->lte('r.dateTime', ':startDateTime'))
            ->andWhere($builder->expr()->gte('r.dateTime', ':endDateTime'))
            ->orderBy('r.dateTime', $order)
            ->setParameter('startDateTime', $startDateTime)
            ->setParameter('endDateTime', $endDateTime);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findByCityAndMonth(City $city, \DateTime $monthDateTime): array
    {
        $startDateTime = DateTimeUtil::getMonthStartDateTime($monthDateTime);
        $endDateTime = DateTimeUtil::getMonthEndDateTime($monthDateTime);

        $builder = $this->createQueryBuilder('r');

        $builder->select('r')
            ->where($builder->expr()->gte('r.dateTime', ':startDateTime'))
            ->andWhere($builder->expr()->lte('r.dateTime', ':endDateTime'))
            ->andWhere($builder->expr()->eq('r.city', ':city'))
            ->addOrderBy('r.dateTime', 'ASC')
            ->setParameter('startDateTime', $startDateTime)
            ->setParameter('endDateTime', $endDateTime)
            ->setParameter('city', $city);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findByDate(\DateTime $date): array
    {
        $startDateTime = DateTimeUtil::getDayStartDateTime($date);
        $endDateTime = DateTimeUtil::getDayEndDateTime($date);

        $builder = $this->createQueryBuilder('r');

        $builder->select('r')
            ->where($builder->expr()->gte('r.dateTime', ':startDateTime'))
            ->andWhere($builder->expr()->lte('r.dateTime', ':endDateTime'))
            ->addOrderBy('r.dateTime', 'ASC')
            ->setParameter('startDateTime', $startDateTime)
            ->setParameter('endDateTime', $endDateTime);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findOneByCityAndSlug(City $city, string $slug): ?Ride
    {
        $builder = $this->createQueryBuilder('r');

        $builder->select('r')
            ->where($builder->expr()->eq('r.city', ':city'))
            ->andWhere($builder->expr()->eq('r.slug', ':slug'))
            ->setParameter('city', $city)
            ->setParameter('slug', $slug);

        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findOneByCitySlugAndSlug(string $citySlug, string $rideSlug): ?Ride
    {
        $builder = $this->createQueryBuilder('r');

        $builder
            ->select('r')
            ->join('r.city', 'c')
            ->join('c.slugs', 'cs')
            ->where($builder->expr()->eq('cs.slug', ':citySlug'))
            ->andWhere($builder->expr()->eq('r.slug', ':rideSlug'))
            ->setParameter('citySlug', $citySlug)
            ->setParameter('rideSlug', $rideSlug);

        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findFrontpageRides(): array
    {
        $startDateTime = new \DateTime();
        $startDateTimeInterval = new \DateInterval('P8W');
        $startDateTime->add($startDateTimeInterval);

        $endDateTime = new \DateTime();
        $endDateTimeInterval = new \DateInterval('P1D');
        $endDateTime->sub($endDateTimeInterval);

        $builder = $this->createQueryBuilder('r');

        $builder->select('r, city')
            ->join('r.city', 'city')
            ->where($builder->expr()->lte('r.dateTime', ':startDateTime'))
            ->andWhere($builder->expr()->gte('r.dateTime', ':endDateTime'))
            ->andWhere($builder->expr()->eq('city.enabled', ':enabled'))
            ->andWhere(
                $builder->expr()->orX(
                    $builder->expr()->gte('city.activityScore', ':threshold'),
                    $builder->expr()->isNull('city.activityScore')
                )
            )
            ->addOrderBy('r.dateTime', 'ASC')
            ->addOrderBy('r.city', 'ASC')
            ->setParameter('startDateTime', $startDateTime)
            ->setParameter('endDateTime', $endDateTime)
            ->setParameter('enabled', true)
            ->setParameter('threshold', CityRepository::ACTIVITY_SCORE_THRESHOLD);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findEstimatedRides(?int $year = null, ?int $month = null): array
    {
        $builder = $this->createQueryBuilder('ride');

        $builder
            ->select('ride')
            ->addSelect('city')
            ->join('ride.estimates', 'estimates')
            ->join('ride.city', 'city')
            ->orderBy('ride.dateTime', 'ASC')
            ->addOrderBy('city.city', 'ASC');

        if ($year && $month) {
            $builder
                ->andWhere($builder->expr()->eq('MONTH(ride.dateTime)', $month))
                ->andWhere($builder->expr()->eq('YEAR(ride.dateTime)', $year));
        }

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findRidesInInterval(?\DateTime $startDateTime = null, ?\DateTime $endDateTime = null)
    {
        if (!$startDateTime) {
            $startDateTime = new \DateTime();
        }

        if (!$endDateTime) {
            $endDate = new \DateTime();
            $dayInterval = new \DateInterval('P1M');
            $endDateTime = $endDate->add($dayInterval);
        }

        $builder = $this->createQueryBuilder('ride');

        $builder
            ->select('ride')
            ->where($builder->expr()->gt('ride.dateTime', ':startDateTime'))
            ->andWhere($builder->expr()->lt('ride.dateTime', ':endDateTime'))
            ->addOrderBy('ride.dateTime', 'ASC')
            ->setParameter('startDateTime', $startDateTime)
            ->setParameter('endDateTime', $endDateTime);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findRidesByDateTimeMonth(\DateTime $dateTime): array
    {
        $startDateTime = DateTimeUtil::getMonthStartDateTime($dateTime);
        $endDateTime = DateTimeUtil::getMonthEndDateTime($dateTime);

        return $this->findRidesInInterval($startDateTime, $endDateTime);
    }

    public function findCityRideByDate(City $city, \DateTime $dateTime): ?Ride
    {
        $fromDateTime = DateTimeUtil::getDayStartDateTime($dateTime);
        $untilDateTime = DateTimeUtil::getDayEndDateTime($dateTime);

        $builder = $this->createQueryBuilder('r');

        $builder
            ->select('r')
            ->where($builder->expr()->eq('r.city', ':city'))
            ->andWhere($builder->expr()->gt('r.dateTime', ':fromDateTime'))
            ->andWhere($builder->expr()->lt('r.dateTime', ':untilDateTime'))
            ->setParameter('city', $city)
            ->setParameter('fromDateTime', $fromDateTime)
            ->setParameter('untilDateTime', $untilDateTime)
            ->setMaxResults(1);

        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findByCitySlugAndRideDate(string $citySlug, string $rideDate): ?Ride
    {
        $rideDateTime = new \DateTime($rideDate);
        $fromDateTime = DateTimeUtil::getDayStartDateTime($rideDateTime);
        $untilDateTime = DateTimeUtil::getDayEndDateTime($rideDateTime);

        $builder = $this->createQueryBuilder('r');

        $builder
            ->select('r')
            ->join('r.city', 'c')
            ->join('c.slugs', 'cs')
            ->where($builder->expr()->eq('cs.slug', ':citySlug'))
            ->andWhere($builder->expr()->gt('r.dateTime', ':fromDateTime'))
            ->andWhere($builder->expr()->lt('r.dateTime', ':untilDateTime'))
            ->setParameter('citySlug', $citySlug)
            ->setParameter('fromDateTime', $fromDateTime)
            ->setParameter('untilDateTime', $untilDateTime);

        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }

    public function getPreviousRideWithSubrides(Ride $ride): array
    {
        $builder = $this->createQueryBuilder('r');

        $builder
            ->select('r')
            ->join('r.subrides', 'sr')
            ->where($builder->expr()->lt('r.dateTime', ':dateTime'))
            ->andWhere($builder->expr()->eq('r.city', ':city'))
            ->addOrderBy('r.dateTime', 'DESC')
            ->setMaxResults(1)
            ->setParameter('city', $ride->getCity())
            ->setParameter('dateTime', $ride->getDateTime());

        $query = $builder->getQuery();

        $result = $query->getOneOrNullResult();

        return $result;
    }

    public function getLocationsForCity(City $city): array
    {
        $builder = $this->createQueryBuilder('r');

        $builder
            ->select([
                'r.location',
                'r.latitude',
                'r.longitude'
            ])
            ->where($builder->expr()->eq('r.city', ':city'))
            ->andWhere($builder->expr()->isNotNull('r.location'))
            ->orderBy('r.location', 'ASC')
            ->groupBy('r.location')
            ->setParameter('city', $city);

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function countRidesByCity(City $city): int
    {
        $builder = $this->createQueryBuilder('r');

        $builder
            ->select('COUNT(r)')
            ->where($builder->expr()->eq('r.city', ':city'))
            ->setParameter('city', $city);

        $query = $builder->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    public function findRidesWithFacebook(): array
    {
        $builder = $this->createQueryBuilder('r');

        $builder
            ->select('r')
            ->where($builder->expr()->isNotNull('r.facebook'))
            ->orderBy('r.dateTime', 'DESC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findRidesWithFacebookInInterval(
        ?\DateTime $startDateTime = null,
        ?\DateTime $endDateTime = null
    ): array {
        if (!$startDateTime) {
            $startDateTime = new \DateTime();
        }

        if (!$endDateTime) {
            $endDate = new \DateTime();
            $dayInterval = new \DateInterval('P1M');
            $endDateTime = $endDate->add($dayInterval);
        }

        $builder = $this->createQueryBuilder('r');

        $builder
            ->select('r')
            ->where($builder->expr()->gt('r.dateTime', ':startDateTime'))
            ->andWhere($builder->expr()->lt('r.dateTime', ':endDateTime'))
            ->andWhere($builder->expr()->isNotNull('r.facebook'))
            ->orderBy('r.dateTime', 'DESC')
            ->setParameter('startDateTime', $startDateTime)
            ->setParameter('endDateTime', $endDateTime);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findRidesWithoutStatisticsForCity(City $city, bool $pastOnly = true): array
    {
        $builder = $this->createQueryBuilder('ride');

        $builder->select('ride');
        $builder->where($builder->expr()->orX(
            $builder->expr()->isNull('ride.estimatedParticipants'),
            $builder->expr()->isNull('ride.estimatedDuration'),
            $builder->expr()->isNull('ride.estimatedDistance')
        ));

        $builder->andWhere($builder->expr()->eq('ride.city', $city->getId()));

        if ($pastOnly) {
            $dateTime = new \DateTime();

            $builder->andWhere($builder->expr()->lt('ride.dateTime', ':pastDateTime'))
                ->setParameter('pastDateTime', $dateTime);
        }
        $builder->orderBy('ride.dateTime', 'DESC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findRides(
        ?\DateTimeInterface $fromDateTime = null,
        ?\DateTimeInterface $untilDateTime = null,
        ?City $city = null,
        ?Region $region = null
    ): array {
        $builder = $this->createQueryBuilder('ride');

        $builder
            ->select('ride')
            ->join('ride.city', 'city')
            ->where($builder->expr()->eq('city.enabled', ':enabled'))
            ->setParameter('enabled', true);

        if ($city) {
            $builder
                ->andWhere($builder->expr()->eq('city', ':city'))
                ->setParameter('city', $city);
        }

        if ($region) {
            $builder
                ->leftJoin('city.region', 'region1')
                ->leftJoin('region1.parent', 'region2')
                ->leftJoin('region2.parent', 'region3')
                ->andWhere(
                    $builder->expr()->orX(
                        $builder->expr()->eq('region1', ':region'),
                        $builder->expr()->eq('region2', ':region'),
                        $builder->expr()->eq('region3', ':region')
                    )
                )
                ->setParameter('region', $region);
        }

        if ($fromDateTime) {
            $builder
                ->andWhere($builder->expr()->gt('ride.dateTime', ':fromDateTime'))
                ->setParameter('fromDateTime', $fromDateTime);
        }

        if ($fromDateTime) {
            $builder
                ->andWhere($builder->expr()->lt('ride.dateTime', ':untilDateTime'))
                ->setParameter('untilDateTime', $untilDateTime);
        }

        $builder
            ->addOrderBy('city.city', 'ASC')
            ->addOrderBy('ride.dateTime', 'DESC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findRidesInRegionInInterval(
        Region $region,
        ?\DateTime $startDateTime = null,
        ?\DateTime $endDateTime = null
    ): array {
        $builder = $this->createQueryBuilder('ride');

        $builder->select(['ride', 'city', 'region1']);

        $builder->join('ride.city', 'city');
        $builder->join('city.region', 'region1');

        $builder->where($builder->expr()->eq('region1.parent', $region->getId()));

        if ($startDateTime) {
            $builder->andWhere($builder->expr()->gt('ride.dateTime', ':startDateTime'))
                ->setParameter('startDateTime', $startDateTime);
        }

        if ($endDateTime) {
            $builder->andWhere($builder->expr()->lt('ride.dateTime', ':endDateTime'))
                ->setParameter('endDateTime', $endDateTime);
        }

        $builder->addOrderBy('city.city', 'ASC');
        $builder->addOrderBy('ride.dateTime', 'DESC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findForTimelineRideEditCollector(
        ?\DateTime $startDateTime = null,
        ?\DateTime $endDateTime = null,
        ?int $limit = null
    ): array {
        $builder = $this->createQueryBuilder('r');

        $builder
            ->select('r')
            ->where($builder->expr()->isNotNull('r.updatedAt'));

        if ($startDateTime) {
            $builder
                ->andWhere($builder->expr()->gte('r.updatedAt', ':startDateTime'))
                ->setParameter('startDateTime', $startDateTime);
        }

        if ($endDateTime) {
            $builder
                ->andWhere($builder->expr()->lte('r.updatedAt', ':endDateTime'))
                ->setParameter('endDateTime', $endDateTime);
        }

        if ($limit) {
            $builder
                ->setMaxResults($limit);
        }

        $builder
            ->addOrderBy('r.updatedAt', 'DESC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findRidesByCycleInInterval(
        CityCycle $cityCycle,
        \DateTime $startDateTime,
        \DateTime $endDateTime
    ): array {
        $builder = $this->createQueryBuilder('r');

        $builder
            ->select('r')
            ->where($builder->expr()->gt('r.dateTime', ':startDateTime'))
            ->andWhere($builder->expr()->lt('r.dateTime', ':endDateTime'))
            ->andWhere($builder->expr()->eq('r.cycle', ':cityCycle'))
            ->addOrderBy('r.dateTime', 'ASC')
            ->setParameter('startDateTime', $startDateTime)
            ->setParameter('endDateTime', $endDateTime)
            ->setParameter('cityCycle', $cityCycle);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findMostPopularRides(int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('r');

        $qb
            ->addOrderBy('r.estimatedParticipants', 'DESC')
            ->where($qb->expr()->isNotNull('r.estimatedParticipants'))
            ->setMaxResults($limit);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function findLongestDistanceRides(int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('r');

        $qb
            ->addOrderBy('r.estimatedDistance', 'DESC')
            ->where($qb->expr()->isNotNull('r.estimatedDistance'))
            ->setMaxResults($limit);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function findLongestDurationRides(int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('r');

        $qb
            ->addOrderBy('r.estimatedDuration', 'DESC')
            ->where($qb->expr()->isNotNull('r.estimatedDuration'))
            ->setMaxResults($limit);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function findByCycle(CityCycle $cityCycle, string $orderDireciton = 'DESC'): array
    {
        $builder = $this->createQueryBuilder('r');

        $builder
            ->select('r')
            ->where($builder->expr()->eq('r.cycle', ':cycle'))
            ->addOrderBy('r.dateTime', $orderDireciton)
            ->setParameter('cycle', $cityCycle);


        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findRidesForLocation(Location $location, float $radiusInMeters = 500, int $limit = 25): array
    {
        if (!$location->getLatitude() || !$location->getLongitude()) {
            return [];
        }

        $latitude = $location->getLatitude();
        $longitude = $location->getLongitude();
        $earthRadius = 6371000;

        $rsm = new ResultSetMapping();

        $rsm->addEntityResult(Ride::class, 'r');
        $rsm->addFieldResult('r', 'id', 'id');
        $rsm->addFieldResult('r', 'dateTime', 'dateTime');
        $rsm->addFieldResult('r', 'latitude', 'latitude');
        $rsm->addFieldResult('r', 'longitude', 'longitude');
        $rsm->addFieldResult('r', 'title', 'title');

        $rsm->addJoinedEntityResult(City::class, 'c', 'r', 'city');
        $rsm->addFieldResult('c', 'c_id', 'id');

        $rsm->addJoinedEntityResult(CitySlug::class, 'cs', 'c', 'mainSlug');
        $rsm->addFieldResult('cs', 'cs_id', 'id');
        $rsm->addFieldResult('cs', 'cs_slug', 'slug');

        $sql = <<<SQL
SELECT 
    r.id,
    r.dateTime,
    r.latitude,
    r.longitude,
    r.title,
    r.city_id,
    c.id AS c_id,
    cs.id AS cs_id,
    cs.slug AS cs_slug,
    (
        $earthRadius * acos(
            cos(radians(:latitude)) * cos(radians(r.latitude)) *
            cos(radians(r.longitude) - radians(:longitude)) +
            sin(radians(:latitude)) * sin(radians(r.latitude))
        )
    ) AS distance
FROM ride r
INNER JOIN city c ON r.city_id = c.id
INNER JOIN cityslug cs ON cs.id = c.main_slug_id
HAVING distance <= :radius
ORDER BY r.dateTime DESC
LIMIT :limit
SQL;

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('latitude', $latitude);
        $query->setParameter('longitude', $longitude);
        $query->setParameter('radius', $radiusInMeters);
        $query->setParameter('limit', $limit, \Doctrine\DBAL\ParameterType::INTEGER);

        return $query->getResult();
    }

    public function searchByQuery(string $query, int $maxResults = 50): array
    {
        $qb = $this->createQueryBuilder('r');
        $expr = $qb->expr();

        if ($query !== '') {
            $qb->where(
                $expr->orX(
                    $expr->like('r.title', ':q'),
                    $expr->like('r.description', ':q'),
                    $expr->like('r.location', ':q')
                )
            )->setParameter('q', sprintf('%%%s%%', $query));
        }

        return $qb
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult()
        ;
    }
}
