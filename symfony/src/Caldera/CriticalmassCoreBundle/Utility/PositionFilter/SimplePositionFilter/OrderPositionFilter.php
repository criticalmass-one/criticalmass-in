<?php

namespace Caldera\CriticalmassCoreBundle\Utility\PositionFilter\SimplePositionFilter;

use Caldera\CriticalmassCoreBundle\Entity as Entity;

/**
 * Ordnet die Ergebnisliste der Positionsdaten absteigend nach ihrem Alter.
 */
class OrderPositionFilter extends SimplePositionFilter
{
	/**
	 * {@inheritDoc}
	 */
	public function buildQuery($queryBuilder)
	{
		return $queryBuilder->orderBy('p.creationDateTime', 'DESC');
	}
}