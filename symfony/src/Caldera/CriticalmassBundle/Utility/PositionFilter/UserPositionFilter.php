<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;

/**
 * Dieser Filter sortiert die uebergebenen Position zunaechst nach ihren Benut-
 * zern und gibt anschliessend die letzten beiden Positionen des Nutzers zu-
 * rueck.
 */
class UserPositionFilter extends BasePositionFilter
{
	/**
	 * Implementierung der Filterung.
	 */
	public function process()
	{
		// enthaelt spaeter die Positionen nach Benutzer und Uhrzeit sortiert
		$positionSortedByUser = array();

		// wird die Ergebnis-Positionen nach der Filterung enthalten
		$filteredPositions = array();

		// erst einmal werden alle Positionen nach Benutzer und Uhrzeit sortiert
		foreach ($this->positionArray->getPositions() as $position)
		{
			$positionSortedByUser[$position->getUser()->getId()][$position->getCreationDateTime()->format("Y-m-d-H-i-s")] = $position;
		}

		// fuer jeden Benutzer werden nun die letzten beiden Positionen herausgesucht
		foreach ($positionSortedByUser as $positionSortedByUserKey => $positionSortedByUserValue)
		{
			// zunaechst werden die Positionen nach ihrer Uhrzeit sortiert
			ksort($positionSortedByUserValue);

			// die neueste Position auslesen
			$firstPosition = array_pop($positionSortedByUserValue);

			// jetzt wird die zweitneueste Position gesucht, die nicht mit der neuesten
			// Position identisch ist
			do
			{
				$secondPosition = array_pop($positionSortedByUserValue);
			}
			// die Schleife laeuft, bis eine nicht-identische Positon gefunden wurde oder aber
			// das Array leer ist
			while ($firstPosition->isEqual($secondPosition) && count($positionSortedByUserValue));

			// beide Positionen dem Array hinzufuegen
			$filteredPositions[] = $firstPosition;
			$filteredPositions[] = $secondPosition;
		}

		// nun muessen die neuen Werte im Filter gespeichert werden
		$this->positionArray->setPositions($filteredPositions);
	}
}