<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter\SimplePositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;

/**
 * Wenn der Godmode einer Tour eingeschaltet ist, werden in diesem Filter alle
 * Positionsdaten entfernt, die nicht vom Administrator abgesendet worden sind.
 */
class GodmodePositionFilter extends SimplePositionFilter
{
	/**
	 * {@inheritDoc}
	 */
	public function buildQuery($queryBuilder)
	{
		if ($this->ride->getGodmode())
		{
			return $queryBuilder->andWhere('p.user = 7');
		}

		return $queryBuilder;
	}
}