<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Region;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

/**
 * Dieses Repository erbt vom EntityRepository und stellt eine zusaetzliche Me-
 * thode bereit, um Staedte nach ihrer Entfernung zu einer angegebenen Koor-
 * dinate sortiert auszugeben.
 */
class CityRepository extends EntityRepository
{
	/**
	 * Findet abhängig vom übergebenen Breiten- und Längengrad die nächsten
	 * Städte in der Reihenfolge ihrer Entfernung zum angegebenen Standort.
	 *
	 * @param Float $latitude: Breitengrad
	 * @param Float $longitude: Längengrad
	 *
	 * @return Array: Liste der Staedte.
	 */
	public function findNearestedByLocation($latitude, $longitude)
	{
        $query = $this->getEntityManager()->createQuery("SELECT c AS city, SQRT((c.latitude - ".$latitude.") * (c.latitude - ".$latitude.") + (c.longitude - ".$longitude.") * (c.longitude - ".$longitude.")) AS distance FROM CalderaCriticalmassCoreBundle:City c ORDER BY distance ASC");

        return $query->getResult();
	}

    public function findCitiesOfRegion(Region $region)
    {
        $builder = $this->createQueryBuilder('city');

        $builder->select('city');

        $builder->where($builder->expr()->eq('city.enabled', 1));
        $builder->andWhere($builder->expr()->eq('city.isArchived', 0));
        $builder->andWhere($builder->expr()->eq('city.region', $region->getId()));

        $builder->orderBy('city.city', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function countCitiesOfRegion(Region $region)
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

    public function findEnabledCities()
    {
        return $this->findCities();
    }

    public function findCities()
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create();

        $criteria->where(
            $expr->andX(
                $expr->eq('enabled', true),
                $expr->eq('isArchived', false)

            )
        );

        $criteria->orderBy(array('city' => 'ASC'));

        return $this->matching($criteria);
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
}

