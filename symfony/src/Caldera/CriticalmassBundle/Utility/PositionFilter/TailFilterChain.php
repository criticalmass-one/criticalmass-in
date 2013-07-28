<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;

class TailFilterChain extends BasePositionFilterChain
{
	public function execute()
	{
		$this->filters[] = new AccuracyPositionFilter($this->ride);
		/*$this->filters[] = new DoublePositionFilter($this->ride);
		$this->filters[] = new UserPositionFilter($this->ride);*/

		foreach ($this->filters as $filter)
		{
			$filter->setPositionArray($this->positionArray);
			$filter->process();
			$this->positionArray = $filter->getPositionArray();
		}
	}
}