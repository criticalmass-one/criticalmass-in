<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;

class PositionFilterChain
{
	protected $ride;

	protected $filters = array();

	protected $positionArray;

	public function __construct(Entity\Ride $ride, $positions)
	{
		$this->ride = $ride;
		$this->positionArray = new PositionArray($this->positions);
	}

	public function execute()
	{
		$this->filters[] = new AccuracyPositionFilter();
		$this->filters[] = new DoublePositionFilter();
		$this->filters[] = new UserPositionFilter();
	}

	public function getPositions()
	{
		return $this->positions;
	}
}