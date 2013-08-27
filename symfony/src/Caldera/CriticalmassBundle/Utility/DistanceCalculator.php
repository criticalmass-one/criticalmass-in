<?php

namespace Caldera\CriticalmassBundle\Utility;

use Caldera\CriticalmassBundle\Entity as Entity;

/**
 * Diese Hilfsklasse berechnet die Entfernungen zwischen zwei Punkten. Die ver-
 * schiedenen Methoden nehmen dabei Staedte, Positionen oder Koordinatenangaben
 * als Parameter an.
 */
class DistanceCalculator
{
	/**
	 * Bestimmt die Entfernung zwischen zwei Städten und gibt das Ergebnis zu-
	 * zurück.
	 *
	 * @param Entity\City $city1 Entität der ersten Stadt
	 * @param Entity\City $city2 Entität der zweiten Stadt
	 *
	 * @return double Berechnete Entfernung zwischen den beiden Städten
	 */
	public function calculateDistanceFromCityToCity(Entity\City $city1, Entity\City $city2)
	{
		return $this->calculateDistanceFromCoordToCoord($city1->getLatitude(), $city2->getLatitude(), $city1->getLongitude(), $city2->getLongitude());
	}

	/**
	 * Bestimmt die Entfernung zwischen zwei Punkten und gibt das Ergebnis zu-
	 * zurück.
	 *
	 * @since 2013-07-25
	 *
	 * @param Entity\Position $position1 Position der ersten Stadt
	 * @param Entity\Position $position2 Position der zweiten Stadt
	 *
	 * @return double Berechnete Entfernung zwischen den beiden Positionen
	 */
	public function calculateDistanceFromPositionToPosition(Entity\Position $position1, Entity\Position $position2)
	{
		return $this->calculateDistanceFromCoordToCoord($position1->getLatitude(), $position2->getLatitude(), $position1->getLongitude(), $position2->getLongitude());
	}

	/**
	 * Berechnung der Entfernung zwischen zwei Punkten auf einer als Ebene ange-
	 * nommenen Erdoberfläche.
	 *
	 * @param $latitude1 Breitengrad des ersten Punktes
	 * @param $latitude2 Breitengrad des zweiten Punktes
	 * @param $longitude1 Längengrad des ersten Punktes
	 * @param $longitude2 Längengrad des zweiten Punktes
	 *
	 * @return double Berechnete Entfernung zwischen beiden Punkten
	 */
	public function calculateDistanceFromCoordToCoord($latitude1, $latitude2, $longitude1, $longitude2)
	{
		// wenn beide Koordinaten identisch sind, laesst sich die Distanz nicht berechnen
		if (($latitude1 == $latitude2) &&
				($longitude1 == $longitude2))
		{
			return 0.0;
		}

		// Radius der Erde
		$radius = 6371.0;

		// Koordinaten in radiale Werte umwandeln
		$latitude1 = deg2rad($latitude1);
		$latitude2 = deg2rad($latitude2);
		$longitude1 = deg2rad($longitude1);
		$longitude2 = deg2rad($longitude2);

		// Distanzberechnung
		$distance = acos(sin($latitude1) * sin($latitude2) + 
								cos($latitude1) * cos($latitude2) * cos($longitude2 - $longitude1)) *
								$radius;

		return $distance;
	}
}