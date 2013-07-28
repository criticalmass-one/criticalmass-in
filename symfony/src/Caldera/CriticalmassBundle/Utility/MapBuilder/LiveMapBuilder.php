<?php

namespace Caldera\CriticalmassBundle\Utility\MapBuilder;

use Caldera\CriticalmassBundle\Utility\PositionFilter as PositionFilter;
use Caldera\CriticalmassBundle\Utility\MapElement as MapElement;
use Caldera\CriticalmassBundle\Utility as Utility;

class LiveMapBuilder extends BaseMapBuilder
{
	public function getUserCounter()
	{/*
		$users = array();

		foreach ($this->mainPositions as $position)
		{
			if (!in_array($position->getUser(), $users))
			{
				$users[] = $position->getUser();
			}
		}

		return count($users);*/
		return 107;
	}

	public function getAverageSpeed()
	{
		$positions = $this->mainPositions;

		$dc = new Utility\DistanceCalculator();
		$distance = $dc->calculateDistanceFromPositionToPosition($positions[0], $positions[1]);
		$time = $positions[1]->getCreationDateTime()->format('U') - $positions[0]->getCreationDateTime()->format('U');

		$averageSpeed = $distance / $time;

		$averageSpeed *= 3600;

		return round($averageSpeed, 2);
	}

	public function getZoomFactor()
	{
		$minX = null;
		$maxX = null;
		$minY = null;
		$maxY = null;

		foreach ($this->mainPositions as $position)
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

	public function calculateMainPositions()
	{
		$psf = new PositionFilter\PositionFilterChain();
		$psf->setRide($this->ride);
		$psf->setPositions($this->positions);
		$psf->execute();

		$this->mainPositions = $psf->getPositions();
	}

	public function calculateAdditionalPositions()
	{
		$psf = new PositionFilter\TailFilterChain();
		$psf->setRide($this->ride);
		$psf->setPositions($this->positions);
		$psf->execute();

		$this->additionalPositions = $psf->getPositions();
	}

	public function calculateMapCenter($coordinateFunction)
	{
		$min = null;
		$max = null;

		foreach ($this->mainPositions as $position)
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