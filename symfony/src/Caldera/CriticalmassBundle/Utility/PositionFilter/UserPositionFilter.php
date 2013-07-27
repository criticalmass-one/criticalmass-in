<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;

class UserPositionFilter extends BasePositionFilter
{
	public function process()
	{
		$positionSortedByUser = array();
		$filteredPositions = array();

		foreach ($this->positionArray->getPositions() as $position)
		{
			$positionSortedByUser[$position->getUser()->getId()][$position->getCreationDateTime()->format("Y-m-d-H-i-s")] = $position;
		}

		foreach ($positionSortedByUser as $positionSortedByUserKey => $positionSortedByUserValue)
		{
			ksort($positionSortedByUserValue);

			$firstPosition = array_pop($positionSortedByUserValue);

			do
			{
				$secondPosition = array_pop($positionSortedByUserValue);
			}
			while ($firstPosition->isEqual($secondPosition));

			$filteredPositions[] = $firstPosition;
			$filteredPositions[] = $secondPosition;
		}

		$this->positionArray->setPositions($filteredPositions);
	}
}