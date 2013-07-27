<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;

class UserPositionFilter extends BasePositionFilter
{
	public function process()
	{
		foreach ($this->positionArray->getPositions() as $key => $position)
		{
			if ($position->getAccuracy() > 30)
			{
				$this->positionArray->deletePosition($key);
			}
		}
	}
}