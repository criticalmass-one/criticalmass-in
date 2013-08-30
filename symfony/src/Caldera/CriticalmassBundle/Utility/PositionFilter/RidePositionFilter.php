<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;

class RidePositionFilter extends BasePositionFilter
{
	public function process()
	{
	}

	public function buildQuery($queryBuilder)
	{
		return $queryBuilder->where("p.ride = ".$this->ride->getId());
	}
}
