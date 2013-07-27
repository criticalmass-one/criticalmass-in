<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;

abstract class BasePositionFilter
{
	protected $ride;
	protected $positions = array();

	public function __construct(Entity\Ride $ride)
	{
		$this->ride = $ride;
	}

	public function setPositions($positions)
	{
		$this->positions = $positions;
	}

	public function getPositions()
	{
		return $this->positions;
	}

	public abstract function process();
}