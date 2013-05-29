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
		return 53.57033623530256;
	}

	public function getMapCenterLongitude()
	{
		return 9.719623122674422;
	}

	public function getPositions()
	{
		return array(
			'city1' => array(
				'latitude' => 53.57033623530256,
				'longitude' => 9.719623122674422
				),
			'city2' => array(
				'latitude' => 53.57033623130256,
				'longitude' => 9.719623128674422
				)
		);
	}

	public function getMapData()
	{
		return array(
			'mapcenter' => $mph->getMapCenter(),
			'zoom' => $mph->getZoomFactor(),
			'positions' => $mph->getPositions()
		);
	}
}