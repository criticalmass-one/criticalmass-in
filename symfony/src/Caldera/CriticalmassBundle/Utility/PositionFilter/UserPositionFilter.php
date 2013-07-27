<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;

class UserPositionFilter extends BasePositionFilter
{
	public function process()
	{
		/*
		$sortedPositions = array();
		$filteredPositions = array();

		foreach ($this->positions as $position)
		{
			$sortedPositions[$position->getUser()->getId()][$position->getCreationDateTime()->format("Y-m-d-H-i-s")] = $position;
		}

		foreach ($sortedPositions as $sortedPositionUser => $sortedPositionArray)
		{
			if (count($sortedPositionArray) < 2)
			{
				unset($sortedPositions[$sortedPositionUser]);
			}
			else
			{
				ksort($sortedPositionArray);

				$newPositions[$sortedPositionUser] = array_pop($sortedPositionArray);

				do
				{
					$oldPositions[$sortedPositionUser] = array_pop($sortedPositionArray);
				}
				while ($newPositions[$sortedPositionUser]->isEqual($oldPositions[$sortedPositionUser]));
			}
		}*/
	}
}