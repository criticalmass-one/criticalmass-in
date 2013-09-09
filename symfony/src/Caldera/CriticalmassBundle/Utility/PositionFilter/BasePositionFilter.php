<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;
use Caldera\CriticalmassBundle\Utility\PositionArray as PositionArray;

/**
 * Aus dieser Klasse werden die verschiedenen Filter abgeleitet, die fuer die
 * Sortierung und Filterung der darzustellenden Positionen notwendig sind. Je-
 * der Filter speichert eine Instanz einer Ride-Entitaet, um seine Information-
 * en gegen die Tour abgleichen zu koennen. Ausserdem werden zwei Methoden be-
 * reitgestellt, um das PositionArray in den Filter hinein und nach der Bear-
 * beitung wieder herausleiten zu koennen.
 */
abstract class BasePositionFilter
{
	/**
	 * Instanz einer Ride-Entitaet.
	 */
	protected $ride;

	/**
	 * Dem Konstruktor wird jeweils eine Instanz einer Ride-Entitaet uebergeben.
	 *
	 * @param Entity\Ride $ride: Instanz einer Ride-Entitaet
	 */
	public function __construct(Entity\Ride $ride)
	{
		$this->ride = $ride;
	}

}