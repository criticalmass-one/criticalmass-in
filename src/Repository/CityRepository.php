<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Region;
use Doctrine\ORM\EntityRepository;

/**
 * Dieses Repository erbt vom EntityRepository und stellt eine zusaetzliche Me-
 * thode bereit, um Staedte nach ihrer Entfernung zu einer angegebenen Koor-
 * dinate sortiert auszugeben.
 */
class CityRepository extends EntityRepository
{
    public function findCitiesWithFacebook()
    {
        $builder = $this->createQueryBuilder('city');

        $builder->select('city');

        $builder->where($builder->expr()->isNotNull('city.facebook'));

        $builder->orderBy('city.city', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findCitiesOfRegion(Region $region)
    {
        $builder = $this->createQueryBuilder('city');

        $builder->select('city');

        $builder->where($builder->expr()->eq('city.enabled', 1));
        $builder->andWhere($builder->expr()->eq('city.region', $region->getId()));
        $builder->andWhere($builder->expr()->neq('city.latitude', 0));
        $builder->andWhere($builder->expr()->neq('city.longitude', 0));

        $builder->orderBy('city.city', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function countChildrenCitiesOfRegion(Region $region)
    {
        $builder = $this->createQueryBuilder('city');

        $builder->select('COUNT(city)');

        $builder->leftJoin('city.region', 'region1');
        $builder->leftJoin('region1.parent', 'region2');
        $builder->leftJoin('region2.parent', 'region3');

        $builder->where($builder->expr()->eq('city.enabled', 1));

        $builder->andWhere(
            $builder->expr()->orX(
                $builder->expr()->eq('region1.id', $region->getId()),
                $builder->expr()->eq('region2.id', $region->getId()),
                $builder->expr()->eq('region3.id', $region->getId())
            )
        );

        $query = $builder->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    public function findChildrenCitiesOfRegion(Region $region)
    {
        $builder = $this->createQueryBuilder('city');

        $builder->select('city');

        $builder->leftJoin('city.region', 'region1');
        $builder->leftJoin('region1.parent', 'region2');
        $builder->leftJoin('region2.parent', 'region3');

        $builder->where($builder->expr()->eq('city.enabled', 1));

        $builder->andWhere(
            $builder->expr()->orX(
                $builder->expr()->eq('region1.id', $region->getId()),
                $builder->expr()->eq('region2.id', $region->getId()),
                $builder->expr()->eq('region3.id', $region->getId())
            )
        );

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

    public function findCitiesWithBoard()
    {
        $builder = $this->createQueryBuilder('city');

        $builder->select('city');

        $builder->where($builder->expr()->eq('city.enabled', 1));
        $builder->andWhere($builder->expr()->eq('city.enableBoard', 1));

        $builder->orderBy('city.city', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findCitiesByAverageParticipants($limit = 10)
    {
        $query = $this->getEntityManager()->createQuery("SELECT IDENTITY(r.city) AS city, c.city AS cityName, SUM(r.estimatedParticipants) / COUNT(c.id) AS averageParticipants FROM App:Ride r JOIN r.city c GROUP BY r.city ORDER BY averageParticipants DESC")->setMaxResults($limit);

        return $query->getResult();
    }

    public function findForTimelineCityEditCollector(
        \DateTime $startDateTime = null,
        \DateTime $endDateTime = null,
        int $limit = null
    ): array {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('c')
            ->where($builder->expr()->isNotNull('c.updatedAt'))
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
            $builder
                ->setMaxResults($limit);
        }

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findForTimelineCityCreatedCollector(
        \DateTime $startDateTime = null,
        \DateTime $endDateTime = null,
        int $limit = null
    ): array {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('c')
            ->where($builder->expr()->isNull('c.updatedAt'))
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
}

