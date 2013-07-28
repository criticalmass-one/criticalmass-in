<?php

namespace Caldera\CriticalmassBundle\Utility\MapBuilder;

use \Caldera\CriticalmassBundle\Utility\MapElement as MapElement;
use \Caldera\CriticalmassBundle\Entity as Entity;

abstract class BaseMapBuilder
{
	protected $positions = array();

	protected $mainPositions;

	protected $additionalPositions;

	protected $ride;

	public function __construct(Entity\Ride $ride, $positions)
	{
		$this->ride = $ride;
		$this->positions = $positions;
	}

	public abstract function getUserCounter();

	public abstract function getAverageSpeed();

	public abstract function getZoomFactor();

	public abstract function getMapCenterLatitude();

	public abstract function getMapCenterLongitude();

	public abstract function calculateMainPositions();

	public abstract function calculateAdditionalPositions();
}