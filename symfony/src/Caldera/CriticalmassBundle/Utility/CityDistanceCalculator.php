<?php

namespace Caldera\CriticalmassBundle\Utility;

use Caldera\CriticalmassBundle\Entity as Entity;

class CityDistanceCalculator
{
	public function calculateKilometreDistanceFromCityToCity(Entity\City $city1, Entity\City $city2)
	{
		return $this->calculateDistanceFromCityToCity($city1, $city2) * 75.;
	}

	public function calculateDistanceFromCityToCity(Entity\City $city1, Entity\City $city2)
	{
		return $this->calculateDistanceFromCoordToCoord($city1->getLatitude(), $city2->getLatitude(), $city1->getLongitude(), $city2->getLongitude());
	}

	public function calculateDistanceFromCoordToCoord($latitude1, $latitude2, $longitude1, $longitude2)
	{
		return sqrt(pow($latitude1 - $latitude2, 2) + pow($longitude1 - $longitude2, 2));
	}

}