<?php

namespace Caldera\CriticalmassCoreBundle\Utility\PositionFilter\SimplePositionFilter;

use Caldera\CriticalmassCoreBundle\Entity as Entity;

/**
 * Entfernt alle Positionsdaten, die vom Client als veraltet eingeschaetzt wor-
 * den sind, da das Timestamp-Feld veraltet ist.
 */
class TimePositionFilter extends SimplePositionFilter
{
	/**
	 * {@inheritDoc}
	 */
	public function buildQuery($queryBuilder)
	{
		return $queryBuilder->andWhere("p.creationDateTime - p.timestamp < 30");
	}
}