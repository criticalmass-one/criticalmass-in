<?php

namespace Caldera\CriticalmassBundle\Utility\MapElement;

use \Caldera\CriticalmassBundle\Entity\Position;

/**
 * Diese Klasse repraesentiert ein Kreiselement fuer die grafische Darstellung
 * in der eingebetteten Karte der Live-Ansicht.
 */
class CircleMapElement extends BaseMapElement
{
	/**
	 * Positions-Entitaet, aus der der Mittelpunkt berechnet wird.
	 */
	protected $centerPosition;

	/**
	 * Radius des Kreises.
	 */
	protected $radius;

	/**
	 * Farbe des Kreisrandes.
	 */
	protected $strokeColor = '#ff0000';

	/**
	 * Farbe der Kreisflaeche.
	 */
	protected $fillColor = '#ff0000';

	/**
	 * Transparenz des Kreisrandes.
	 */
	protected $strokeOpacity = 0.8;

	/**
	 * Transparenz der Kreisflaeche.
	 */
	protected $fillOpacity = 0.35;

	/**
	 * Breite des Kreisrandes.
	 */
	protected $strokeWeight = 2.0;

	/**
	 * Der Konstruktor nimmt eine Position-Entitaet und den Radius der Kreisflae-
	 * che entgegen.
	 *
	 * @param Position $centerPosition: Positions-Entitaet
	 * @param Integer $radius: Radius des Kreises
	 */
	public function __construct(Position $centerPosition, $radius)
	{
		$this->centerPosition = $centerPosition;
		$this->radius = $radius;
	}

	/**
	 * Konstruiert eine eindeutige ID aus den Koordinaten und dem Radius des Krei-
	 * ses.
	 *
	 * @return String: Eindeutige ID des Elements.
	 */
	public function getId()
	{
		return 'circle-'.$this->centerPosition->getLatitude().'-'.$this->centerPosition->getLongitude().'-'.$this->radius;
	}

	/**
	 * Gibt ein Array mit geometrischen und grafischen Informationen über den zu
	 * zeichnenden Kreis zurueck, die auf dem Client mit JavaScript ausgewertet
	 * werden koennen.
	 *
	 * @return Array: Informationen zum Zeichnen des Kreises
	 */
	public function draw()
	{
		return array(
			'id' => $this->getId(),
			'type' => 'circle',
			'latitude' => $this->centerPosition->getLatitude(),
			'longitude' => $this->centerPosition->getLongitude(),
			//'radius' => $this->radius,
			'radius' => 50,
			'strokeColor' => $this->strokeColor,
			'fillColor' => $this->fillColor,
			'strokeOpacity' => $this->strokeOpacity,
			'fillOpacity' => $this->fillOpacity,
			'strokeWeight' => $this->strokeWeight
		);
	}
}
