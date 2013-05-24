<?php

namespace Caldera\CriticalmassBundle\Utility;

use Caldera\CriticalmassBundle\Entity as Entity;

class CityDistanceCalculator
{
	/**
	 * Bestimmt die Entfernung zwischen zwei Städten und gibt das Ergebnis als
	 * Kilometerangabe zurück.
	 *
	 * @param Entity\City $city1 Entität der ersten Stadt
	 * @param Entity\City $city2 Entität der zweiten Stadt
	 *
	 * @return double Berechnete Entfernung zwischen den beiden Städten in Kilo-
	 * metern
	 */
	public function calculateKilometreDistanceFromCityToCity(Entity\City $city1, Entity\City $city2)
	{
		return $this->calculateDistanceFromCityToCity($city1, $city2) * 75.;
	}

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
		return sqrt(pow($latitude1 - $latitude2, 2) + pow($longitude1 - $longitude2, 2));
	}
}