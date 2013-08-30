<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;

/**
 * Dieser Filter entfernt alle Positionen aus dem Array und soll natuerlich nur
 * zu Testzwecken eingesetzt werden.
 */
class DeletePositionFilter extends BasePositionFilter
{
	/**
	 * Entfernung aller Positionen.
	 */
	public function process()
	{
		// alle Positionen durchlaufen
		foreach ($this->positionArray->getPositions() as $key => $position)
		{
			// und loeschen
			$this->positionArray->deletePosition($key);
		}
	}

	public function buildQuery($queryBuilder)
	{
		return $queryBuilder;
		//return $queryBuilder->setMaxResults(0);
	}
}