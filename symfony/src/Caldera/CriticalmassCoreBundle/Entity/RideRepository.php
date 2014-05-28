<?php

namespace Caldera\CriticalmassCoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RideRepository extends EntityRepository
{
	public function findCurrentRides()
	{
		$query = $this->getEntityManager()->createQuery("SELECT r AS ride FROM CalderaCriticalmassCoreBundle:Ride r GROUP BY r.city ORDER BY r.dateTime DESC");

		return $query->getResult();
	}
}

