<?php

namespace Caldera\CriticalmassBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RideRepository extends EntityRepository
{
	public function findLatest()
	{
		$ride = $this->getEntityManager()->createQuery("SELECT r FROM CalderaCriticalmassBundle:Ride r ORDER BY r.date DESC")->getResult();
		return $ride[0];
	}
}