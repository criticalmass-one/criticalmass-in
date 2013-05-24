<?php

namespace Caldera\CriticalmassBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RideRepository extends EntityRepository
{
	public function findLatestForCity(City $city)
	{
		$ride = $this->getEntityManager()->createQuery("SELECT r FROM CalderaCriticalmassBundle:Ride r WHERE r.city_id = ".$city->getId()." ORDER BY r.date DESC")->getResult();
		return $ride[0];
	}
}