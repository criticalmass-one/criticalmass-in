<?php

namespace Caldera\CriticalmassBundle\Utility;

abstract class BaseCoordFilter
{
	private $ride;
	private $positions = array();

	public function __construct(Entity\Ride $ride, $positions)
	{
		$this->ride = $ride;
		$this->positions = $positions;
	}

	public abstract function getCalculatedPositions();
}