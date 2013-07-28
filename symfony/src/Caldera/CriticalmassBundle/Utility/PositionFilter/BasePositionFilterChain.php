<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;

abstract class BasePositionFilterChain
{
	protected $ride;

	protected $filters = array();

	protected $positionArray;

	public function setRide(Entity\Ride $ride)
	{
		$this->ride = $ride;
	}

	public function setPositions($positions)
	{
		$this->positionArray = new PositionArray($positions);
	}

	public function getPositions()
	{
		return $this->positionArray->getPositions();
	}

	public abstract function execute();
}