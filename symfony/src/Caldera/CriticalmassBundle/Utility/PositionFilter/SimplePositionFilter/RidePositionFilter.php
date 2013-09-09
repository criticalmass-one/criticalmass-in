<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter\SimplePositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;

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
