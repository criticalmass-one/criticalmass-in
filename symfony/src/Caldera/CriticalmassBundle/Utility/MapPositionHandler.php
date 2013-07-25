<?php

namespace Caldera\CriticalmassBundle\Utility;

use \Caldera\CriticalmassBundle\Entity as Entity;
use \Caldera\CriticalmassBundle\Utility as Utility;


class MapPositionHandler
{
	protected $ride;
	protected $positions = array();
	protected $positionFilter;

	public function __construct(Entity\Ride $ride, Utility\BasePositionFilter $positionFilter)
	{
		$this->ride = $ride;
		$this->positionFilter = $positionFilter;
	}

	public function setPositions($positions)
	{
		$this->positions = $positions;
		$this->positionFilter->setPositions($positions);
	}

	public function getUserCounter()
	{
		$users = array();

		foreach ($this->positions as $position)
		{
			if (!in_array($position->getUser(), $users))
			{
				$users[] = $position->getUser();
			}
		}

		return count($users);
	}
	public function getZoomFactor()
	{
		$minX = null;
		$maxX = null;
		$minY = null;
		$maxY = null;

		foreach ($this->positionFilter->getCalculatedPositions() as $position)
		{
			if (!isset($minX) && !isset($maxX) && !isset($minY) && !isset($maxY))
			{
				$minX = $position->getLatitude();
				$maxX = $position->getLatitude();
				$minY = $position->getLongitude();
				$maxY = $position->getLongitude();
			}
			else
			{
				if ($minX > $position->getLatitude())
				{
					$minX = $position->getLatitude();
				}

				if ($maxX < $position->getLatitude())
				{
					$maxX = $position->getLatitude();
				}

				if ($minY > $position->getLongitude())
				{
					$minY = $position->getLongitude();
				}

				if ($maxY < $position->getLongitude())
				{
					$maxY = $position->getLongitude();
				}
			}
		}

		$distanceX = $maxX - $minX;
		$distanceY = $maxY - $minY;

		$distance = ($distanceX > $distanceY ? $distanceX : $distanceY) + 0.001;

		$zoomFactor = floor(log(960 * 360 / $distance / 256)) + 1;

		return $zoomFactor;
	}

	public function getMapCenterLatitude()
	{
		return $this->calculateMapCenter("getLatitude");
	}

	public function getMapCenterLongitude()
	{
		return $this->calculateMapCenter("getLongitude");
	}

	public function getMainPositions()
	{
		$resultArray = array();
		$counter = 0;

		foreach ($this->positionFilter->getCalculatedPositions() as $position)
		{
			$newId = "position-".$counter;

			$resultArray[$newId] = array(
				'id' => $newId,
				'latitude' => $position->getLatitude(),
				'longitude' => $position->getLongitude(),
				'radius' => 100,
				'strokeColor' => '#ff0000',
				'fillColor' => 'ff0000',
				'strokeOpacity' => 0.8,
				'fillOpacity' => 0.35,
				'strokeWeight' => 2
			);

			++$counter;
		}

		return $resultArray;
	}

	public function getAdditionalPositions()
	{
		$resultArray = array();

		foreach ($this->positions as $position)
		{
			$newId = "position-".$position->getId();

			$resultArray[$newId] = array(
				'id' => $newId,
				'latitude' => $position->getLatitude(),
				'longitude' => $position->getLongitude(),
				'radius' => 10,
				'strokeColor' => '#ff0000',
				'fillColor' => 'ff0000',
				'strokeOpacity' => 0.8,
				'fillOpacity' => 0.35,
				'strokeWeight' => 2
			);
		}


		return $resultArray;
	}

	public function calculateMapCenter($coordinateFunction)
	{
		$min = null;
		$max = null;

		foreach ($this->positionFilter->getCalculatedPositions() as $position)
		{
			if (!isset($min) && !isset($max))
			{
				$min = $position->$coordinateFunction();
				$max = $position->$coordinateFunction();
			}
			elseif ($min > $position->$coordinateFunction())
			{
				$min = $position->$coordinateFunction();
			}
			elseif ($max < $position->$coordinateFunction())
			{
				$max = $position->$coordinateFunction();
			}
		}

		return $min + ($max - $min) / 2.0;
	}
}
