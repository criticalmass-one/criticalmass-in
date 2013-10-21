<?php

namespace Caldera\CriticalmassCoreBundle\Utility;

use Caldera\CriticalmassCoreBundle\Entity as Entity;

/**
 * Diese Klasse laesst sich zu einem Container instanzieren, um Positionsdaten
 * besser als in einem simplen Array verwalten zu koennen. Es werden verschie-
 * dene Operationen zur Manipulation der Daten angeboten.
 */
class PositionArray
{
	/**
	 * Speichert das eigentliche Array ab, das die Positionsdaten enthaelt.
	 */
	protected $positions = array();

	/**
	 * Erstellt ein PositionArray mit dem uebergebenen Array.
	 *
	 * @param $positions: Liste mit Positionsdaten
	 */
	public function __construct($positions = array())
	{
		$this->positions = $positions;
	}

	/**
	 * Gibt das Array mit den Positionsdaten zurueck.
	 *
	 * @return Array mit den Positionsdaten
	 */
	public function getPositions()
	{
		return $this->positions;
	}

	/**
	 * Ueberschreibt das Array mit den uebergebenen Positionsdaten.
	 *
	 * @param $positions: Array mit den neuen Positionsdaten
	 */
	public function setPositions($positions)
	{
		$this->positions = $positions;
	}

	/**
	 * Gibt ein einzelnes, mit dem uebergebenen Schluessel identifiziertes Posi-
	 * tionsdatum zurueck.
	 *
	 * @param $key: Schluessel innerhalb des Arrays
	 *
	 * @return Entity\Position: Angefragtes Positionsdatum
	 */
	public function getPosition($key)
	{
		return $this->positions[$key];
	}

	/**
	 * Ueberschreibt ein einzelnes Positionsdatum an der genannten Stelle im Ar-
	 * ray.
	 *
	 * @param $key: Schluessel des Datums
	 * @param Entity\Position $position: Einzufuegendes Positionsdatum
	 */
	public function setPosition($key, Entity\Position $position)
	{
		$this->positions[$key] = $position;
	}

	/**
	 * Fuegt ein Positionsdatum in das Array ein.
	 *
	 * @param Entity\Position $position: Einzufuegendes Positionsdatum
	 */
	public function addPosition(Entity\Position $position)
	{
		$this->positions[] = $position;
	}

	/**
	 * Loescht das Positionsdatum hinter dem angegebenen Schluessel.
	 *
	 * @param $key: Schluessel des zu loeschenden Positionsdatums
	 */
	public function deletePosition($key)
	{
		unset($this->positions[$key]);
	}

	/**
	 * Gibt die Anzahl der gespeicherten Positionsdaten zurueck.
	 *
	 * @return Integer: Anzahl der gespeicherten Positionsdaten
	 */
	public function countPositions()
	{
		return count($this->positions);
	}

    public function merge(PositionArray $positionArray)
    {
        $this->positions = array_merge($this->positions, $positionArray->getPositions());

        return $this;
    }
}