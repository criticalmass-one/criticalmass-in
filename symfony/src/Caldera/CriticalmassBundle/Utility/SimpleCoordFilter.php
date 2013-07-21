<?php

namespace Caldera\CriticalmassBundle\Utility;

class SimpleCoordFilter extends BaseCoordFilter
{
	public function getCalculatedPositions()
	{
		$sortedPositions = array();

		$oldPositions = array();
		$newPositions = array();

		foreach ($this->positions as $position)
		{
			$sortedPositions[$position->getUser()->getId()][$position->getCreatedDateTime()->format("Y-m-d-h-i-s")] = $position;
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

				$newPositions[$sortedPositionsUser] = array_pop($sortedPositionArray);
				$oldPositions[$sortedPositionsUser] = array_pop($sortedPositionArray);
			}
		}




		$tmpValueX = 0.0;
		$tmpValueY = 0.0;

		foreach ($oldPositions as $position)
		{
			$tmpValueX += $position->getLatitude();
			$tmpValueY += $position->getLongitude();
		}

		$oldPositionX = $tmpValueX / (float) count($oldPositions);
		$oldPositionY = $tmpValueY / (float) count($oldPositions);




		$tmpValueX = 0.0;
		$tmpValueY = 0.0;

		foreach ($newPositions as $position)
		{
			$tmpValueX += $position->getLatitude();
			$tmpValueY += $position->getLongitude();
		}

		$newPositionX = $tmpValueX / (float) count($newPositions);
		$newPositionY = $tmpValueY / (float) count($newPositions);


		return array(array($oldPositionX, $oldPositionY), array($newPositionX, $newPositionY));
	}
}