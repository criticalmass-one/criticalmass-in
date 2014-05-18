<?php

namespace Caldera\CriticalmassCoreBundle\Utility\PositionFilterChain;

use Caldera\CriticalmassCoreBundle\Entity as Entity;
use Caldera\CriticalmassCoreBundle\Utility\PositionFilter\SimplePositionFilter as SimplePositionFilter;
use Caldera\CriticalmassCoreBundle\Utility\PositionFilter\ComplexPositionFilter as ComplexPositionFilter;

/**
 * Diese Filterkette laedt die Positionsdaten aus der Datenbank, die fuer die
 * eigentliche Berechnung des Mittelpunktes des Teilnehmerfeldes benoetigt wer-
 * den.
 */
class PositionFilterChain extends BasePositionFilterChain
{
	/**
	 * {@inheritDoc}
	 */
	public function registerFilter()
	{
		$this->filters[] = new SimplePositionFilter\RidePositionFilter($this->ride);
		$this->filters[] = new SimplePositionFilter\AccuracyPositionFilter($this->ride);
		$this->filters[] = new SimplePositionFilter\Limit25PositionFilter($this->ride);
		$this->filters[] = new SimplePositionFilter\OrderPositionFilter($this->ride);
		$this->filters[] = new ComplexPositionFilter\UserPositionFilter($this->ride);
	}
}
