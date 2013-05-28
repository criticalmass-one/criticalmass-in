<?php

namespace Caldera\CriticalmassBundle\Utility;

use Caldera\CriticalmassBundle\Entity as Entity;

class MapPositionHandler
{
	protected Entity\Ride $ride;

	public function __construct(Entity\Ride $ride)
	{
		$this->setRide($ride);
	}

	public function setRide(Entiry\Ride $ride)
	{
		$this->ride = $ride;
	}

	public function getZoomFactor()
	{
		return 10;
	}
}