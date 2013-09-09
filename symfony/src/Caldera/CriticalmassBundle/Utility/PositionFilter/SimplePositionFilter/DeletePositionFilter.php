<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter\SimplePositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;

/**
 * Entfernt alle Positionsdaten aus der Ergebnismenge und sollte natuerlich nur
 * zu Testzwecken eingesetzt werden.
 */
class DeletePositionFilter extends SimplePositionFilter
{
	/**
	 * {@inheritDoc}
	 */
	public function buildQuery($queryBuilder)
	{
		return $queryBuilder->setMaxResults(0);
	}
}