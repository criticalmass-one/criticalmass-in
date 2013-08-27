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

	protected $doctrine;

	public function setRide(Entity\Ride $ride)
	{
		$this->ride = $ride;

		return $this;
	}

	public function setDoctrine(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
	{
		$this->doctrine = $doctrine;
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

	public function hasComplexFilter()
	{
		$complex = false;

		foreach ($this->filters as $filter)
		{
			if ($filter->isComplexFilter())
			{
				$complex = true;
			}
		}

		return $complex;
	}

	protected function executeSimpleFilterChain()
	{
		
	}

	protected function executeComplexFilterChain()
	{
		foreach ($this->filters as $filter)
		{
			$filter->setPositionArray($this->positionArray);
			$filter->process();
			$this->positionArray = $filter->getPositionArray();
		}

		return $this;
	}

	public function execute()
	{
		$this->registerFilter();

		if ($this->hasComplexFilter())
		{
			return $this->executeComplexFilterChain();
		}
		else
		{
			return $this->executeSimpleFilterChain();
		}
	}
}