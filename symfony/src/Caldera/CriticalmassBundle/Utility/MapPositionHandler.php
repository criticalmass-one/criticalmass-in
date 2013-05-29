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
		return 10;
	}

	public function getMapCenterLatitude()
	{
		return $this->calculateMapCenterLatitude();
	}

	public function getMapCenterLongitude()
	{
		return 9.719623122674422;
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

	public function calculateMapCenterLatitude()
	{
		$min = null;
		$max = null;

		foreach ($this->getPositions() as $position)
		{
			if (!isset($min) && !isset($max))
			{
				$min = $position->getLatitude();
				$max = $position->getLatitude();
			}
			elseif ($min > $position->getLatitude())
			{
				$min = $position->getLatitude();
			}
			elseif ($max < $position->getLatitude())
			{
				$max = $position->getLatitude();
			}
		}

		return $min + ($max - $min) / 2.0;
	}
}