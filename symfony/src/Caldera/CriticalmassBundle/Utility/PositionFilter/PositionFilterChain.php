<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;

class PositionFilterChain extends BasePositionFilterChain
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
		$this->positionArray = new PositionArray($this->positions);
	}

	public function execute()
	{
		$this->filters[] = new AccuracyPositionFilter($this->ride);
		$this->filters[] = new DoublePositionFilter($this->ride);
		$this->filters[] = new UserPositionFilter($this->ride);
	}

	public function getPositions()
	{
		return $this->positions;
	}
}