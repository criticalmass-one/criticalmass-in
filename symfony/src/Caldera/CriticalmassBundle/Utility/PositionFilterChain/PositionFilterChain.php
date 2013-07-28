<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilterChain;

use Caldera\CriticalmassBundle\Entity as Entity;
use Caldera\CriticalmassBundle\Utility\PositionFilter as PositionFilter;

class PositionFilterChain extends BasePositionFilterChain
{
	public function execute()
	{
		$this->filters[] = new PositionFilter\AccuracyPositionFilter($this->ride);
		$this->filters[] = new PositionFilter\DoublePositionFilter($this->ride);
		$this->filters[] = new PositionFilter\UserPositionFilter($this->ride);

		foreach ($this->filters as $filter)
		{
			$filter->setPositionArray($this->positionArray);
			$filter->process();
			$this->positionArray = $filter->getPositionArray();
		}

		return $this;
	}
}