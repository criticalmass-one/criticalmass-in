<?php

namespace Caldera\CriticalmassBundle\Utility;

use \Caldera\CriticalmassBundle\Entity as Entity;

class MapPositionHandler
{
	protected $ride;

	public function __construct(Entity\Ride $ride)
	{
		$this->setRide($ride);
	}

	public function setRide(Entity\Ride $ride)
	{
		$this->ride = $ride;
	}

	public function getZoomFactor()
	{
		return 10;
	}

	public function getMapCenter()
	{
		return array(
			'latitude' => $this->getMapCenterLatitude(),
			'longitude' => $this->getMapCenterLongitude()
		);
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
}