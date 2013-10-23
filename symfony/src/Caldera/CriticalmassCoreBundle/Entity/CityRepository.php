<?php

namespace Caldera\CriticalmassCoreBundle\Entity;

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
		$cities = $this->getEntityManager()->createQuery("SELECT c AS city, SQRT((c.latitude - ".$latitude.") * (c.latitude - ".$latitude.") + (c.longitude - ".$longitude.") * (c.longitude - ".$longitude.")) AS distance FROM CalderaCriticalmassCoreBundle:City c ORDER BY distance ASC")->getResult();

		return $cities;
	}
}

