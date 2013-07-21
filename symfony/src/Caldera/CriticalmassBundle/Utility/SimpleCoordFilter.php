<?php

namespace Caldera\CriticalmassBundle\Utility;

use Caldera\CriticalmassBundle\Entity as Entity;

class SimpleCoordFilter extends BaseCoordFilter
{
	public function __construct(Entity\Ride $ride, $positions)
	{
		parent::__construct($ride, $positions);
	}

	public function getCalculatedPositions()
	{
		$sortedPositions = array();

		$oldPositions = array();
		$newPositions = array();

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
				krsort($sortedPositionArray);

				$newPositions[$sortedPositionUser] = array_pop($sortedPositionArray);
				$oldPositions[$sortedPositionUser] = array_pop($sortedPositionArray);
			}
		}



		$oldPosition = new Entity\Position();

		$tmpValueX = 0.0;
		$tmpValueY = 0.0;

		foreach ($oldPositions as $position)
		{
			$tmpValueX += $position->getLatitude();
			$tmpValueY += $position->getLongitude();
		}

		$oldPosition->setLatitude($tmpValueX / (float) count($oldPositions));
		$oldPosition->setLongitude($tmpValueY / (float) count($oldPositions));


		$newPosition = new Entity\Position();

		$tmpValueX = 0.0;
		$tmpValueY = 0.0;

		foreach ($newPositions as $position)
		{
			$tmpValueX += $position->getLatitude();
			$tmpValueY += $position->getLongitude();
		}

		$newPosition->setLatitude($tmpValueX / (float) count($newPositions));
		$newPosition->setLongitude($tmpValueY / (float) count($newPositions));


		return array($oldPosition, $newPosition);
	}
}