<?php

namespace Caldera\CriticalmassCoreBundle\Utility\PositionFilter\SimplePositionFilter;

use Caldera\CriticalmassCoreBundle\Entity as Entity;

/**
 * Filtert alle Positionsdaten nach der Zugehoerigkeit zu der ausgewaehlten
 * Tour.
 */
class RidePositionFilter extends SimplePositionFilter
{
	/**
	 * {@inheritDoc}
	 */
	public function buildQuery($queryBuilder)
	{
		return $queryBuilder->where("p.ride = ".$this->ride->getId());
	}
}
