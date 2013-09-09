<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilterChain;

use Caldera\CriticalmassBundle\Utility\PositionFilter\SimplePositionFilter as SimplePositionFilter;
use Caldera\CriticalmassBundle\Utility\PositionFilter\ComplexPositionFilter as ComplexPositionFilter;

/**
 * Diese Filterkette laedt alle zusaetzlichen Positionsdaten, die ebenfalls auf
 * der Karte angezeigt werden sollen.
 */
class TailPositionFilterChain extends BasePositionFilterChain
{
	/**
	 * {@inheritDoc}
	 */
	public function registerFilter()
	{
		$this->filters[] = new SimplePositionFilter\RidePositionFilter($this->ride);
		$this->filters[] = new SimplePositionFilter\AccuracyPositionFilter($this->ride);
		$this->filters[] = new SimplePositionFilter\LimitPositionFilter($this->ride);
		$this->filters[] = new SimplePositionFilter\OrderPositionFilter($this->ride);
	}
}