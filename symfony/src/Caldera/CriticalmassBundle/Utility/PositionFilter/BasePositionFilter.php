<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;

abstract class BasePositionFilter
{
	protected $ride;
	protected $positionArray;

	public function __construct(Entity\Ride $ride)
	{
		$this->ride = $ride;
	}

	public function setPositionArray(PositionArray $positionArray)
	{
		$this->positionArray = $positionArray;
	}

	public function getPositionArray()
	{
		return $this->positionArray;
	}

	public abstract function process();
}