<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilterChain;

use Caldera\CriticalmassBundle\Entity as Entity;
use Caldera\CriticalmassBundle\Utility as Utility;
use Caldera\CriticalmassBundle\Utility\PositionFilter as PositionFilter;

abstract class BasePositionFilterChain
{
	protected $ride;

	protected $filters = array();

	protected $positionArray;

	public function setRide(Entity\Ride $ride)
	{
		$this->ride = $ride;

		return $this;
	}

	public function setPositions($positions)
	{
		$this->positionArray = new Utility\PositionArray($positions);

		return $this;
	}

	public function getPositions()
	{
		return $this->positionArray->getPositions();
	}

	public function getPositionArray()
	{
		return $this->positionArray;
	}

	public abstract function registerFilter();

	public function execute()
	{
		foreach ($this->filters as $filter)
		{
			$filter->setPositionArray($this->positionArray);
			$filter->process();
			$this->positionArray = $filter->getPositionArray();
		}

		return $this;
	}
}