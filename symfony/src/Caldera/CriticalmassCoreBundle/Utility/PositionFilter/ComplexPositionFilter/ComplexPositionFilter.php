<?php

namespace Caldera\CriticalmassCoreBundle\Utility\PositionFilter\ComplexPositionFilter;

use Caldera\CriticalmassCoreBundle\Entity as Entity;
use Caldera\CriticalmassCoreBundle\Utility\PositionArray as PositionArray;
use Caldera\CriticalmassCoreBundle\Utility\PositionFilter\BasePositionFilter;

abstract class ComplexPositionFilter extends BasePositionFilter
{
	/**
	 * PositionArray mit den Position-Entitaeten.
	 */
	protected $positionArray;

	/**
	 * Diese Methode empfaengt als Parameter ein PositionArray und speichert es
	 * in der Eigenschaft positionArray zur weiteren Verarbeitung ab.
	 *
	 * @param PositionArray $positionArray: Array mit den Positionen
	 */
	public function setPositionArray(PositionArray $positionArray)
	{
		$this->positionArray = $positionArray;
	}

	/**
	 * Gibt das abgespeicherte PositionArray zurueck.
	 *
	 * @return PositionArray: Instanz eines PositionArray
	 */
	public function getPositionArray()
	{
		return $this->positionArray;
	}

	/**
	 * Alle erbenden Klassen muessen die Methode process() implementieren. In die-
	 * ser Methode erfolgt die eigentliche Filter der Positionen.
	 */
	public abstract function process();
}