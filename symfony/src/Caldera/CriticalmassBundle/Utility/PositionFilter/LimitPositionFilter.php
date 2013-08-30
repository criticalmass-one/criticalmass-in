<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;

class LimitPositionFilter extends BasePositionFilter
{
	public function process()
	{
		// alle Positionen durchlaufen
		foreach ($this->positionArray->getPositions() as $key => $position)
		{
			// und loeschen
			$this->positionArray->deletePosition($key);
		}
	}
}