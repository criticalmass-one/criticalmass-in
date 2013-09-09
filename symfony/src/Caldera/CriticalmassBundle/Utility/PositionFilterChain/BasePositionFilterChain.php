<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilterChain;

use Caldera\CriticalmassBundle\Entity as Entity;
use Caldera\CriticalmassBundle\Utility as Utility;
use Caldera\CriticalmassBundle\Utility\PositionFilter as PositionFilter;

/**
 * Diese abstrakte Klasse stellt die Basis fuer eine Filterketten-Implemen-
 * tierung dar. Sie delegiert die Filterung und Sortierung von Positionsdaten
 * an die einzelnen Filter und stellt eine Ergebnismenge brauchbarer Daten fuer
 * die weitere Berechnung bereit.
 */
abstract class BasePositionFilterChain
{
	/**
	 * Speichert die Ride-Entitaet, die bearbeitet werden soll.
	 */
	protected $ride;

	protected $filters = array();

	/**
	 * Speichert die Menge der Positionsdaten.
	 */
	protected $positionArray;

	/**
	 * Zugriff auf die Doctrine-Instanz.
	 */
	protected $doctrine;

	/**
	 * Speichert die uebergebene Ride-Entitaet ab.
	 *
	 * @param Entity\Ride $ride: Zu speichernde Ride-Entitaet
	 *
	 * @return $this
	 */
	public function setRide(Entity\Ride $ride)
	{
		$this->ride = $ride;

		return $this;
	}

	/**
	 * Speichert eine Doctrine-Instanz fuer den Zugriff auf die Datenbank ab.
	 *
	 * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine: Doctrine-Instanz
	 */
	public function setDoctrine(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
	{
		$this->doctrine = $doctrine;
	}

	/**
	 * Speichert die uebergebenen Positionen in einem Array. Als Parameter wird
	 * eine Liste von Positionsdaten erwartet.
	 *
	 * @param Positions: Liste mit Positionsdaten
	 *
	 * @return $this
	 */
	public function setPositions($positions)
	{
		$this->positionArray = new Utility\PositionArray($positions);

		return $this;
	}

	/**
	 * Gibt eine Liste von Positionsdaten vom Typ Array zurueck.
	 *
	 * @return Positionsdaten
	 */
	public function getPositions()
	{
		return $this->positionArray->getPositions();
	}

	/**
	 * Gibt eine Liste von Positionsdaten vom Typ PositionArray zurueck.
	 *
	 * @return PositionArray: Positionsdaten
	 */
	public function getPositionArray()
	{
		return $this->positionArray;
	}

	/**
	 * Diese Methode muss implementiert werden und die einzelnen Filter in das
	 * filters-Array uebertragen. Dabei ist vor allem auf die Reihenfolge der Fil-
	 * ter zu achten, um unerwartetes Verhalten zu vermeiden.
	 */
	public abstract function registerFilter();

	/**
	 * Fuehrt die eigentliche Filterkette aus. Es werden zunaechst die einfachen,
	 * anschliessend die komplexen Filter aufgerufen, um die Positonsdaten aus der
	 * Datenbank zu laden und zu sortieren.
	 *
	 * Das Ergebnis der Filterung steht anschliessend in der Eigenschaft
	 * positionArray zur weiteren Verarbeitung bereit.
	 */
	public function execute()
	{
		// Filter laden
		$this->registerFilter();

		// QueryBuilder konstruieren
		$repository = $this->doctrine->getRepository('CalderaCriticalmassBundle:Position');
		$queryBuilder = $repository->createQueryBuilder('p');

		// alle einfachen Filter ausfuehren, um eine MySQL-Abfrage zusammenzustellen
		foreach ($this->filters as $filter)
		{
			if ($filter instanceof PositionFilter\SimplePositionFilter\SimplePositionFilter)
			{
				$queryBuilder = $filter->buildQuery($queryBuilder);
			}
		}

		// Abfrage ausfuehren
		$positions = $queryBuilder->getQuery()->getResult();

		// PositionArray erstellen
		$this->positionArray = new Utility\PositionArray($positions);

		// nun werden die komplexen Filter ausgefuehrt
		foreach ($this->filters as $filter)
		{
			if ($filter instanceof PositionFilter\ComplexPositionFilter\ComplexPositionFilter)
			{
				$filter->setPositionArray($this->positionArray);
				$filter->process();
				$this->positionArray = $filter->getPositionArray();
			}
		}

	}
}
