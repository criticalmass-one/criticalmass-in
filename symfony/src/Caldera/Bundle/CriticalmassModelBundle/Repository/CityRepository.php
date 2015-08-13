<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

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

    public function findCitiesByAverageParticipants($limit = 10)
    {
        $query = $this->getEntityManager()->createQuery("SELECT IDENTITY(r.city) AS city, c.city AS cityName, SUM(r.estimatedParticipants) / COUNT(c.id) AS averageParticipants FROM CalderaCriticalmassCoreBundle:Ride r JOIN r.city c GROUP BY r.city ORDER BY averageParticipants DESC")->setMaxResults($limit);

        return $query->getResult();
    }
}

