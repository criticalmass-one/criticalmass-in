<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;

class DoublePositionFilter extends BasePositionFilter
{
	public function process()
	{
		foreach ($this->positionArray->getPositions() as $key1 => $position1)
		{
			foreach ($this->positionArray->getPositions() as $key2 => $position2)
			{
				if ($position2->isEqual($position1))
				{
					$this->positionArray->deletePosition($key2);
				}
			}
		}
	}
}