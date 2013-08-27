<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilterChain;

use Caldera\CriticalmassBundle\Utility\PositionFilter as PositionFilter;

class TailPositionFilterChain extends BasePositionFilterChain
{
	public function registerFilter()
	{
		$this->filters[] = new PositionFilter\DeletePositionFilter($this->ride);
	}
}