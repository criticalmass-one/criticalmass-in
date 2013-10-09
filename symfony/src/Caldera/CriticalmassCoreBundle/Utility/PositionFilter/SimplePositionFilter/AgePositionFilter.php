<?php

namespace Caldera\CriticalmassCoreBundle\Utility\PositionFilter\SimplePositionFilter;

use Caldera\CriticalmassCoreBundle\Entity as Entity;

/**
 * Entfernt alle Positionsdaten, die vor mehr als sechs Stunden abgespeichert
 * worden sind.
 */
class AgePositionFilter extends SimplePositionFilter
{
	/**
	 * {@inheritDoc}
	 */
	public function buildQuery($queryBuilder)
	{
		return $queryBuilder->andWhere("p.creationDateTime >= :begin")->setParameter("begin", new \DateTime('-6 hours'));
	}
}