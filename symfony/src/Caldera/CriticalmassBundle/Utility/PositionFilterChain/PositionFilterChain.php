<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilterChain;

use Caldera\CriticalmassBundle\Entity as Entity;
use Caldera\CriticalmassBundle\Utility\PositionFilter as PositionFilter;

class PositionFilterChain extends BasePositionFilterChain
{
	public function registerFilter()
	{
		$this->filters[] = new PositionFilter\AccuracyPositionFilter($this->ride);
		$this->filters[] = new PositionFilter\DoublePositionFilter($this->ride);
		$this->filters[] = new PositionFilter\UserPositionFilter($this->ride);
	}
}