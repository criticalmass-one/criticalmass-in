<?php

namespace Caldera\CriticalmassBundle\Utility;

use \Caldera\CriticalmassBundle\Entity as Entity;

class MapPositionHandler
{
	protected $ride;

	protected $positions = array();

	public function __construct(Entity\Ride $ride, $positions)
	{
		$this->setRide($ride);
		$this->setPositions($positions);
	}

	public function setRide(Entity\Ride $ride)
	{
		$this->ride = $ride;
	}

	public function setPositions($positions)
	{
		$this->positions = $positions;
	}

	public function getZoomFactor()
	{
		$minX = null;
                $maxX = null;
		$minY = null;
		$maxY = null;

                foreach ($this->getPositions() as $position)
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

		$distance = ($distanceX > $distanceY ? $distanceX : $distanceY);

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

	public function getPositions()
	{
		return $this->positions;
	}

	public function getPositionArray()
	{
		$resultArray = array();

		foreach ($this->getPositions() as $position)
		{
			$resultArray["position".$position->getId()] = array(
				'latitude' => $position->getLatitude(),
				'longitude' => $position->getLongitude()
			);
		}

		return $resultArray;
	}

	public function calculateMapCenter($coordinateFunction)
	{
		$min = null;
		$max = null;

		foreach ($this->getPositions() as $position)
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
