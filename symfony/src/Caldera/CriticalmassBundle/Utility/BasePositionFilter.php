<?php

namespace Caldera\CriticalmassBundle\Utility;

use Caldera\CriticalmassBundle\Entity as Entity;

abstract class BasePositionFilter
{
	protected $ride;
	protected $positions = array();

	public function __construct(Entity\Ride $ride, $positions)
	{
		$this->ride = $ride;
		$this->positions = $positions;
	}

	public abstract function getCalculatedPositions();
}