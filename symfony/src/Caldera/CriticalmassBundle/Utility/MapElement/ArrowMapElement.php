<?php

namespace Caldera\CriticalmassBundle\Utility\MapElement;

use \Caldera\CriticalmassBundle\Entity\Position;

/**
 * Diese Klasse wird aus BaseMapElement abgeleitet und stellt auf der Client-
 * Seite einen Pfeil dar. Fuer die Darstellung eines Pfeiles genuegen zwei Ko-
 * ordinaten, die dem Konstruktor uebergeben werden.
 */
class ArrowMapElement extends BaseMapElement
{
	/**
	 * Start-Koordinate des Pfeiles.
	 */
	protected $fromPosition;

	/**
	 * Ziel-Koordinate des Pfeiles.
	 */
	protected $toPosition;

	/**
	 * Dem Konstruktor des Pfeiles werden zwei Position-Entitaeten uebergeben.
	 *
	 * @param Position $fromPosition: Start-Position des Pfeiles
	 * @param Position $toPosition: Ziel-Position des Pfeiles
	 */
	public function __construct(Position $fromPosition, Position $toPosition)
	{
		$this->fromPosition = $fromPosition;
		$this->toPosition = $toPosition;
	}

	/**
	 * Gibt eine eindeutige ID des Pfeiles zurueck, die aus den beiden Koordinaten
	 * gebildet wird.
	 *
	 * @return String: Eindeutige ID des Pfeiles
	 */
	public function getId()
	{
		return 'arrow-'.$this->fromPosition->getLatitude().'-'.$this->fromPosition->getLongitude().'-'.$this->toPosition->getLatitude().'-'.$this->toPosition->getLongitude();
	}

	/**
	 * Gibt ein Array mit Informationen zur grafischen Darstellung des Pfeiles zurueck.
	 *
	 * @return Array: Informationen zur Darstellung des Pfeiles
	 */
	public function draw()
	{
		return array(
			'id' => $this->getId(),
			'type' => 'arrow',
			'fromPosition' => array('latitude' => $this->fromPosition->getLatitude(), 'longitude' => $this->fromPosition->getLongitude()),
			'toPosition' => array('latitude' => $this->toPosition->getLatitude(), 'longitude' => $this->toPosition->getLongitude())
			);
	}
}