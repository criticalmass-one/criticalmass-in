<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;

class RidePositionFilter extends BasePositionFilter
{
	public function process()
	{
		// alle Positionen durchlaufen
		foreach ($this->positionArray->getPositions() as $key => $position)
		{
			if ($position->getRide()->getId() != $this->ride->getId())
			{
				$this->positionArray->deletePosition($key);
			}
		}
	}

	public function buildQuery($queryBuilder)
	{
		return $queryBuilder->where("p.ride = ".$this->ride->getId());
	}
}
