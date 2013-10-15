<?php

namespace Caldera\CriticalmassCoreBundle\Utility\PositionFilterChain;

use Caldera\CriticalmassCoreBundle\Utility\PositionFilter\SimplePositionFilter as SimplePositionFilter;
use Caldera\CriticalmassCoreBundle\Utility\PositionFilter\ComplexPositionFilter as ComplexPositionFilter;

/**
 * Diese Filterkette laedt alle zusaetzlichen Positionsdaten, die ebenfalls auf
 * der Karte angezeigt werden sollen.
 */
class PermanentPositionFilterChain extends BasePositionFilterChain
{
	/**
	 * {@inheritDoc}
	 */
	public function registerFilter()
	{
		$this->filters[] = new SimplePositionFilter\RidePositionFilter($this->ride);
		$this->filters[] = new SimplePositionFilter\OrderPositionFilter($this->ride);
        $this->filters[] = new SimplePositionFilter\PermanentPositionFilter($this->ride);
	}
}