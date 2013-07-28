<?php

namespace Caldera\CriticalmassBundle\Utility;

use \Caldera\CriticalmassBundle\Entity as Entity;
use \Caldera\CriticalmassBundle\Utility as Utility;


class MapPositionHandler
{
	protected $ride;
	protected $positions = array();

	public function __construct(Entity\Ride $ride, $positions)
	{
		$this->ride = $ride;
		$this->positions = $positions;
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

	public function getAverageSpeed()
	{/*
		$positions = $this->positionFilter->getCalculatedPositions();

		$dc = new Utility\DistanceCalculator();
		$distance = $dc->calculateDistanceFromPositionToPosition($positions[0], $positions[1]);
		$time = $positions[1]->getCreationDateTime()->format('U') - $positions[0]->getCreationDateTime()->format('U');

		$averageSpeed = $distance / $time;

		$averageSpeed *= 3600;

		return round($averageSpeed, 2);*/
		return 42.42;
	}

	public function getZoomFactor()
	{
/*		$minX = null;
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

		return $zoomFactor;*/
		return 10;
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
		$psf = new Utility\PositionFilter\PositionFilterChain();
		$psf->setRide($this->ride);
		$psf->setPositions($this->positions);
		$psf->execute();

		$resultArray = array();
		$counter = 0;

		foreach ($psf->getPositions() as $position)
		{
			$circle = new Utility\MapElement\CircleMapElement($position, 100);

			$resultArray['position-'.$counter] = $circle->draw();
			++$counter;
		}

		return $resultArray;
	}

	public function getAdditionalPositions()
	{
		$psf = new Utility\PositionFilter\PositionFilterChain();
		$psf->setRide($this->ride);
		$psf->setPositions($this->positions);
		$psf->execute();

		$resultArray = array();

		foreach ($psf->getPositions() as $position)
		{
			$circle = new Utility\MapElement\CircleMapElement($position, 10);

			$resultArray[$circle->getId()] = $circle->draw();
		}

		return $resultArray;
	}

	public function calculateMapCenter($coordinateFunction)
	{
		$min = null;
		$max = null;

		$psf = new Utility\PositionFilter\PositionFilterChain();
		$psf->setRide($this->ride);
		$psf->setPositions($this->positions);
		$psf->execute();

		foreach ($psf->getPositions() as $position)
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
