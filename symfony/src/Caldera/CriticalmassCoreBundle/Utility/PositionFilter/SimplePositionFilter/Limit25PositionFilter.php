<?php

namespace Caldera\CriticalmassCoreBundle\Utility\PositionFilter\SimplePositionFilter;

use Caldera\CriticalmassCoreBundle\Entity as Entity;

/**
 * Begrenzt die Anzahl der Positionsdaten auf 25.
 */
class Limit25PositionFilter extends SimplePositionFilter
{
	/**
	 * {@inheritDoc}
	 */
	public function buildQuery($queryBuilder)
	{
		return $queryBuilder->setMaxResults(25);
	}
}