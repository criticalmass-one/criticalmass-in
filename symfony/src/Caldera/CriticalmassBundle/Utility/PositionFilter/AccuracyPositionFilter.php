<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;

/**
 * Dieser Filter entfernt alle Positionen aus dem Array, die als ungenauer als
 * 30 Meter angegeben werden.
 */
class AccuracyPositionFilter extends BasePositionFilter
{
	/**
	 * Entfernung aller Positionen, die ungenauer als 30 Meter sind.
	 */
	public function process()
	{
		// alle Positionen durchlaufen
		foreach ($this->positionArray->getPositions() as $key => $position)
		{
			// ist die Genauigkeit schlechter als 30 Meter?
			if ($position->getAccuracy() > 100)
			{
				// dann loeschen
				$this->positionArray->deletePosition($key);
			}
		}
	}
}