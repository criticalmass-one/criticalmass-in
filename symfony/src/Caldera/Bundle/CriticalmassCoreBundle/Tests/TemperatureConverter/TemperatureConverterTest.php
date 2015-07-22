<?php

namespace Caldera\CriticalmassOpenweatherBundle\Tests\TemperatureConverter;

use Caldera\CriticalmassOpenweatherBundle\Utility\TemperatureConverter\TemperatureConverter;
use PHPUnit_Framework_TestCase;

class TemperatureConverterTest extends PHPUnit_Framework_TestCase {

    public function testCelsiusToFahrenheit()
    {
        $celsius = 25.4;

        $tc = new TemperatureConverter();
        $fahrenheit = $tc->setCelsius($celsius)->convert()->getFahrenheit();

        $this->assertEquals(77.72, round($fahrenheit, 2));
    }

    public function testFahrenheitToCelsius()
    {
        $fahrenheit = 63.6;

        $tc = new TemperatureConverter();
        $celsius = $tc->setFahrenheit($fahrenheit)->convert()->getCelsius();

        $this->assertEquals(17.56, round($celsius, 2));
    }
}