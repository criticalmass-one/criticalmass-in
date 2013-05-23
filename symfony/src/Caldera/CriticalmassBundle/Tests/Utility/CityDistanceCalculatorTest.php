<?php

namespace Caldera\CriticalmassBundle\Tests\Utility;

use Caldera\CriticalmassBundle\Utility as Utility;
use Caldera\CriticalmassBundle\Entity as Entity;

class CalculatorTest extends \PHPUnit_Framework_TestCase
{
	public function testCalculateDistanceFromCityToCity()
	{
		$cdc = new Utility\CityDistanceCalculator();

		$hamburg = new Entity\City();
		$bremen = new Entity\City();

		$hamburg->setLatitude(53.550556);
		$hamburg->setLongitude(9.993333);

		$bremen->setLatitude(53.075878);
		$bremen->setLongitude(8.807311);

		$result = $cdc->calculateDistanceFromCityToCity($hamburg, $bremen);

		$this->assertEquals(1.2774847898, $result);
	}
}