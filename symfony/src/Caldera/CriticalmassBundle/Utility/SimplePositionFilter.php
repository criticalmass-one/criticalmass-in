<?php

namespace Caldera\CriticalmassBundle\Utility;

use Caldera\CriticalmassBundle\Entity as Entity;

class SimplePositionFilter extends BasePositionFilter
{
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
				ksort($sortedPositionArray);

				$newPositions[$sortedPositionUser] = array_pop($sortedPositionArray);
				$oldPositions[$sortedPositionUser] = array_pop($sortedPositionArray);
			}
		}



		$oldPosition = new Entity\Position();

		$tmpValueX = 0.0;
		$tmpValueY = 0.0;
		$tmpDateTime = 0;

		foreach ($oldPositions as $position)
		{
			$tmpValueX += $position->getLatitude();
			$tmpValueY += $position->getLongitude();
			$tmpDateTime += $position->getCreationDateTime()->format('U');
		}

		$oldPosition->setLatitude($tmpValueX / (float) count($oldPositions));
		$oldPosition->setLongitude($tmpValueY / (float) count($oldPositions));
		$oldPosition->setCreationDateTime(new \Datetime('@'.$tmpDateTime / (float) count($oldPositions)));


		$newPosition = new Entity\Position();

		$tmpValueX = 0.0;
		$tmpValueY = 0.0;
		$tmpDateTime = 0;

		foreach ($newPositions as $position)
		{
			$tmpValueX += $position->getLatitude();
			$tmpValueY += $position->getLongitude();
			$tmpDateTime += $position->getCreationDateTime()->format('U');
		}

		$newPosition->setLatitude($tmpValueX / (float) count($newPositions));
		$newPosition->setLongitude($tmpValueY / (float) count($newPositions));
		$newPosition->setCreationDateTime(new \DateTime('@'.$tmpDateTime / (float) count($newPositions)));


		return array($oldPosition, $newPosition);
	}
}