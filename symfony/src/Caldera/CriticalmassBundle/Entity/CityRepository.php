<?php

namespace Caldera\CriticalmassBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CityRepository extends EntityRepository
{
	/**
	 * Findet abhängig vom übergebenen Breiten- und Längengrad die nächsten
	 * Städte in der Reihenfolge ihrer Entfernung.
	 *
	 * @param latitude Breitengrad
	 * @param longitude Längengrad
	 */
	public function findNearestedByLocation($latitude, $longitude)
	{
		$cities = $this->getEntityManager()->createQuery("SELECT c AS city, SQRT((c.latitude - ".$latitude.") * (c.latitude - ".$latitude.") + (c.longitude - ".$longitude.") * (c.longitude - ".$longitude.")) AS distance FROM CalderaCriticalmassBundle:City c ORDER BY distance ASC")->getResult();

		return $cities;
	}
}

