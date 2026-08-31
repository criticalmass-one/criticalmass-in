<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\City;
use App\Entity\Region;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    public function findCitiesWithoutWikidataEntityId(): array
    {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('c')
            ->where($builder->expr()->isNull('c.wikidataEntityId'))
            ->orderBy('c.city', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findCitiesWithWikidataEntityId(): array
    {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('c')
            ->where($builder->expr()->isNotNull('c.wikidataEntityId'))
            ->orderBy('c.city', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    /**
     * @deprecated
     */
    public function findCitiesWithFacebook(): array
    {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('c')
            ->where($builder->expr()->isNotNull('c.facebook'))
            ->orderBy('c.city', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findCitiesOfRegion(Region $region): array
    {
        $builder = $this->createQueryBuilder('c');

        $builder->select('c');

        $builder
            ->where($builder->expr()->eq('c.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($builder->expr()->eq('c.region', ':region'))
            ->setParameter('region', $region)
            ->andWhere($builder->expr()->neq('c.latitude', ':notLatitude'))
            ->setParameter('notLatitude', 0)
            ->andWhere($builder->expr()->neq('c.longitude', ':notLongitude'))
            ->setParameter('notLongitude', 0);

        $builder->orderBy('c.city', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function countChildrenCitiesOfRegion(Region $region): int
    {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('COUNT(c)')
            ->leftJoin('c.region', 'r1')
            ->leftJoin('r1.parent', 'r2')
            ->leftJoin('r2.parent', 'r3')
            ->where($builder->expr()->eq('c.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere(
                $builder->expr()->orX(
                   $builder->expr()->eq('r1', ':region1'),
                    $builder->expr()->eq('r2', ':region2'),
                    $builder->expr()->eq('r3', ':region3')
                )
            )
            ->setParameter('region1', $region)
            ->setParameter('region2', $region)
            ->setParameter('region3', $region);

        $query = $builder->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    public function findChildrenCitiesOfRegion(Region $region): array
    {
        $builder = $this->createQueryBuilder('city');

        $builder
            ->select('city')
            ->leftJoin('city.region', 'r1')
            ->leftJoin('r1.parent', 'r2')
            ->leftJoin('r2.parent', 'r3')
            ->where($builder->expr()->eq('city.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere(
                $builder->expr()->orX(
                    $builder->expr()->eq('r1', ':region1'),
                    $builder->expr()->eq('r2', ':region2'),
                    $builder->expr()->eq('r3', ':region3')
                )
            )
            ->setParameter('region1', $region)
            ->setParameter('region2', $region)
            ->setParameter('region3', $region);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findEnabledCities(): array
    {
        return $this->findCities();
    }

    public function findCities(): array
    {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('c')
            ->where($builder->expr()->eq('c.enabled', ':enabled'))
            ->orderBy('c.city', 'ASC')
            ->setParameter('enabled', true);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    /**
     * Cities scoring below this are treated as inactive and disappear from
     * public lists and the frontpage; a NULL score stays visible.
     *
     * Set just above zero on purpose. A measured run over all 739 cities
     * (2026-08-18) produced 695 cities with a score of exactly 0.0 -- no ride
     * participations, photos, tracks or feed items within six months -- while
     * the lowest non-zero score was 0.0441. Anything in between therefore hides
     * exactly the cities with no signal at all, which is what "inactive" is
     * meant to mean, and leaves the cutoff insensitive to small data shifts.
     * A higher threshold would also make visibility depend on the social_feed
     * signal, which is worth up to 0.15 on its own.
     */
    public const ACTIVITY_SCORE_THRESHOLD = 0.01;

    /** @return list<City> */
    public function findActiveCities(): array
    {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('c')
            ->where($builder->expr()->eq('c.enabled', ':enabled'))
            ->andWhere(
                $builder->expr()->orX(
                    $builder->expr()->gte('c.activityScore', ':threshold'),
                    $builder->expr()->isNull('c.activityScore')
                )
            )
            ->orderBy('c.city', 'ASC')
            ->setParameter('enabled', true)
            ->setParameter('threshold', self::ACTIVITY_SCORE_THRESHOLD);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findCitiesWithBoard(): array
    {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('c')
            ->where($builder->expr()->eq('c.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($builder->expr()->eq('c.enableBoard', ':enableBoard'))
            ->setParameter('enableBoard', true)
            ->orderBy('c.city', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findForTimelineCityEditCollector(
        ?\DateTime $startDateTime = null,
        ?\DateTime $endDateTime = null,
        ?int $limit = null
    ): array {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('c')
            ->where($builder->expr()->isNotNull('c.updatedAt'))
            ->andWhere($builder->expr()->isNotNull('c.user'))
            ->addOrderBy('c.updatedAt', 'DESC');

        if ($startDateTime) {
            $builder
                ->andWhere($builder->expr()->gte('c.updatedAt', ':startDateTime'))
                ->setParameter('startDateTime', $startDateTime);
        }

        if ($endDateTime) {
            $builder
                ->andWhere($builder->expr()->lte('c.updatedAt', ':endDateTime'))
                ->setParameter('endDateTime', $endDateTime);
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findForTimelineCityCreatedCollector(
        ?\DateTime $startDateTime = null,
        ?\DateTime $endDateTime = null,
        ?int $limit = null
    ): array {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('c')
            ->where($builder->expr()->isNull('c.updatedAt'))
            ->andWhere($builder->expr()->isNotNull('c.user'))
            ->addOrderBy('c.createdAt', 'DESC');

        if ($startDateTime) {
            $builder
                ->andWhere($builder->expr()->gte('c.createdAt', ':startDateTime'))
                ->setParameter('startDateTime', $startDateTime);
        }

        if ($endDateTime) {
            $builder
                ->andWhere($builder->expr()->lte('c.createdAt', ':endDateTime'))
                ->setParameter('endDateTime', $endDateTime);
        }

        if ($limit) {
            $builder
                ->setMaxResults($limit);
        }

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findCitiesBySlugList(array $slugList): array
    {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('c')
            ->join('c.slugs', 's')
            ->where($builder->expr()->in('s.slug', ':slugList'))
            ->orderBy('c.city', 'ASC')
            ->setParameter('slugList', $slugList);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findPopularCities(int $limit = 10): array
    {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('c')
            ->where($builder->expr()->eq('c.enabled', ':enabled'))
            ->orderBy('c.cityPopulation', 'DESC')
            ->setParameter('enabled', true)
            ->setMaxResults($limit);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findNearCities(City $city, int $size = 15, float $distance = 50.0): array
    {
        if (!$city->getLatitude() || !$city->getLongitude()) {
            return [];
        }

        $em = $this->getEntityManager();

        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addRootEntityFromClassMetadata(City::class, 'c');

        $sql = <<<SQL
SELECT c.*
FROM city c
WHERE c.enabled = 1
  AND c.id != :id
  AND (6371 * acos(
           cos(radians(:lat)) * cos(radians(c.latitude)) * cos(radians(c.longitude) - radians(:lon)) +
           sin(radians(:lat)) * sin(radians(c.latitude))
       )) <= :distance
ORDER BY (6371 * acos(
             cos(radians(:lat)) * cos(radians(c.latitude)) * cos(radians(c.longitude) - radians(:lon)) +
             sin(radians(:lat)) * sin(radians(c.latitude))
         )) ASC
LIMIT :size
SQL;

        $query = $em->createNativeQuery($sql, $rsm);
        $query->setParameter('lat', $city->getLatitude());
        $query->setParameter('lon', $city->getLongitude());
        $query->setParameter('id', $city->getId());
        $query->setParameter('distance', $distance);
        $query->setParameter('size', $size, \Doctrine\DBAL\ParameterType::INTEGER);

        return $query->getResult();
    }

    public function searchByQuery(string $query, int $maxResults = 50): array
    {
        $qb = $this->createQueryBuilder('c');
        $expr = $qb->expr();

        $conditions = [$expr->eq('c.enabled', ':enabled')];

        $qb->setParameter('enabled', true);

        if ($query !== '') {
            $likeExpr = $expr->orX(
                $expr->like('c.title', ':q'),
                $expr->like('c.description', ':q'),
            );
            $conditions[] = $likeExpr;
            $qb->setParameter('q', '%' . $query . '%');
        }

        $qb->where(call_user_func_array([$expr, 'andX'], $conditions))
            ->setMaxResults($maxResults)
        ;

        return $qb->getQuery()->getResult();
    }

}
