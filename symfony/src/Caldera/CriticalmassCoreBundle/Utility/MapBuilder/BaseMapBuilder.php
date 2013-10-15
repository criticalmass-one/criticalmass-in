<?php

namespace Caldera\CriticalmassCoreBundle\Utility\MapBuilder;

use \Caldera\CriticalmassCoreBundle\Utility\MapElement as MapElement;
use \Caldera\CriticalmassCoreBundle\Entity as Entity;

/**
 * Zur Anzeige der Daten und eingeblendeten grafischen Elemente auf der Live-
 * Seite wird ein so genannter MapBuilder verwendet, der von dieser Klasse erbt
 * und verschiedene Methoden implementieren muss.
 */
abstract class BaseMapBuilder
{
	/**
	 * Speicher der Positions-Daten vom Typ PositionArray.
	 */
	protected $positionArray;

	/**
	 * Schnittstelle zum Zugriff auf die Datenbank.
	 */
	protected $doctrine;

	protected $elements = array();

	/**
	 * Speicher fuer die dazugehoerige Ride-Entitaet.
	 */
	protected $ride;

	/**
	 * Erzeugt eine Instanz eines MapBuilders. Erwartet wird die dazugehoerige Ri-
	 * de-Entitaet sowie eine Doctrine-Verbindung.
	 *
	 * @param Entity\Ride $ride: Ride-Entitaet
	 * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine: Zugriff auf eine
	 * Doctrine-Instanz
	 */
	public function __construct(Entity\Ride $ride, \Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
	{
		$this->ride = $ride;

		$this->doctrine = $doctrine;
	}

	/**
	 * Muss zur Anzeige des Benutzerzaehlers implementiert werden.
	 *
	 * @return Integer: Anzahl der Benutzer, deren Positionsdaten in die Berech-
	 * nung eingeflossen sind
	 */
	public abstract function getUserCounter();

	/**
	 * Muss zur Anzeige der Durchschnittsgeschwindigkeit implementiert werden.
	 *
	 * @return Float: Berechnete Durchschnittsgeschwindigkeit de sTeilnehmerfeldes
	 */
	public abstract function getAverageSpeed();

	/**
	 * Muss zur Anzeige einer Karte implementiert werden und den zu verwendenden
	 * Zoom-Faktor zurueckgeben. Gueltig sind Werte zwischen Eins und Zwoelf.
	 *
	 * @return Integer: Zoom-Faktor der eingebetteten Karte
	 */
	public abstract function getZoomFactor();

	/**
	 * Muss zur Angabe des Breitengrades des Mittelpunktes der eingebetteten Kar-
	 * te implementiert werden.
	 *
	 * @return Float: Breitengrad des Mittelpunktes der eingebetteten Karte
	 */
	public abstract function getMapCenterLatitude();

	/**
	 * Muss zur Angabe des Laengengrades des Mittelpunktes der eingebetteten Kar-
	 * te implementiert werden.
	 *
	 * @return Float: Laengengrad des Mittelpunktes der eingebetteten Karte
	 */
	public abstract function getMapCenterLongitude();

	/**
	 * Berechnet die Hauptpositionen zur Berechnung des Mittelpunktes des Teil-
	 * nehmerfeldes.
	 */
	public abstract function calculatePositions();

	/**
	 * Gibt die JSON-Antwort zurueck, die zur Darstellung der Karteninhalte an den
	 * Client geschickt wird. Dazu werden die verschiedenen Informationen in einem
	 * mehrdimensionalen Array abgelegt und in das JSON-Format uebertragen.
	 *
	 * @return String: JSON-Daten zur Darstellung der Karteninhalte
	 */
	public function draw()
	{/*
		 = array();

        $marker = new MapElement\MarkerMapElement($this->ride);
        $this->elements[] = $marker->draw();

        $this->elements = array_merge($this->elements, $this->getMainPositions());
		
		$main = $this->mainPositions->getPositions();

        if (count($main) > 1)
        {
      		$arrow = new MapElement\ArrowMapElement($main[0], $main[1]);
            $elements[] = $arrow->draw();
        }

		$elements = array_merge($elements, $this->getAdditionalPositions());*/

		return array(
			'mapCenter' => array(
				'latitude' => $this->getMapCenterLatitude(),
				'longitude' => $this->getMapCenterLongitude()
				),
				'zoom' => $this->getZoomFactor(),
				'elements' => $this->elements,
				'usercounter' => $this->getUserCounter(),
				'averagespeed' => $this->getAverageSpeed()
			);
	}
}
