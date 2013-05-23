<?php

namespace Caldera\CriticalmassBundle\Utility;

use Caldera\CriticalmassBundle\Entity as Entity;

class CityDistanceCalculator
{
	public function calculateDistanceFromCityToCity(Entity\City $city1, Entity\City $city2)
	{
		return sqrt(pow($city1->getLatitude() - $city2->getLatitude(), 2) + pow($city1->getLongitude() - $city2->getLongitude(), 2));
	}
}