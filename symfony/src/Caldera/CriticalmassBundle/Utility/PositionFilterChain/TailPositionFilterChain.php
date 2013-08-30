<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilterChain;

use Caldera\CriticalmassBundle\Utility\PositionFilter as PositionFilter;

class TailPositionFilterChain extends BasePositionFilterChain
{
	public function registerFilter()
	{
		$this->filters[] = new PositionFilter\RidePositionFilter($this->ride);
		$this->filters[] = new PositionFilter\AccuracyPositionFilter($this->ride);
		$this->filters[] = new PositionFilter\LimitPositionFilter($this->ride);
	}
}