<?php

namespace Caldera\Bundle\CalderaBundle\Repository;

use Caldera\Bundle\CalderaBundle\Entity\Region;
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
        $builder->andWhere($builder->expr()->eq('city.isArchived', 0));

        $builder->orderBy('city.city', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findCitiesOfRegion(Region $region)
    {
        $builder = $this->createQueryBuilder('city');

        $builder->select('city');

        $builder->where($builder->expr()->eq('city.enabled', 1));
        $builder->andWhere($builder->expr()->eq('city.isArchived', 0));
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
        $builder->andWhere($builder->expr()->eq('city.isArchived', 0));

        $builder->andWhere(
            $builder->expr()->orX(
                $builder->expr()->eq('region1.id', $region->getId()),
                $builder->expr()->eq('region2.id', $region->getId()),
                $builder->expr()->eq('region3.id', $region->getId())
            )
        );

        $query = $builder->getQuery();

        return $query->getSingleScalarResult();
    }

    public function findChildrenCitiesOfRegion(Region $region)
    {
        $builder = $this->createQueryBuilder('city');

        $builder->select('city');

        $builder->leftJoin('city.region', 'region1');
        $builder->leftJoin('region1.parent', 'region2');
        $builder->leftJoin('region2.parent', 'region3');

        $builder->where($builder->expr()->eq('city.enabled', 1));
        $builder->andWhere($builder->expr()->eq('city.isArchived', 0));

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

    public function findEnabledCities()
    {
        return $this->findCities();
    }

    public function findCities()
    {
        $builder = $this->createQueryBuilder('city');

        $builder->select('city');

        $builder->where($builder->expr()->eq('city.enabled', 1));
        $builder->andWhere($builder->expr()->eq('city.isArchived', 0));

        $builder->orderBy('city.city', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findCitiesWithBoard()
    {
        $builder = $this->createQueryBuilder('city');

        $builder->select('city');

        $builder->where($builder->expr()->eq('city.enabled', 1));
        $builder->andWhere($builder->expr()->eq('city.isArchived', 0));
        $builder->andWhere($builder->expr()->eq('city.enableBoard', 1));

        $builder->orderBy('city.city', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findCitiesByAverageParticipants($limit = 10)
    {
        $query = $this->getEntityManager()->createQuery("SELECT IDENTITY(r.city) AS city, c.city AS cityName, SUM(r.estimatedParticipants) / COUNT(c.id) AS averageParticipants FROM CalderaCriticalmassCoreBundle:Ride r JOIN r.city c GROUP BY r.city ORDER BY averageParticipants DESC")->setMaxResults($limit);

        return $query->getResult();
    }

    public function findForTimelineCityEditCollector(\DateTime $startDateTime = null, \DateTime $endDateTime = null, $limit = null)
    {
        $builder = $this->createQueryBuilder('city');

        $builder->select('city');

        $builder->where($builder->expr()->eq('city.isArchived', 1));
        $builder->andWhere($builder->expr()->isNotNull('city.archiveUser'));

        if ($startDateTime) {
            $builder->andWhere($builder->expr()->gte('city.archiveDateTime', '\''.$startDateTime->format('Y-m-d H:i:s').'\''));
        }

        if ($endDateTime) {
            $builder->andWhere($builder->expr()->lte('city.archiveDateTime', '\''.$endDateTime->format('Y-m-d H:i:s').'\''));
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $builder->addOrderBy('city.archiveDateTime', 'DESC');

        $builder->addGroupBy('city.user');
        $builder->addGroupBy('city.archiveParent');

        $query = $builder->getQuery();

        return $query->getResult();
    }
}

