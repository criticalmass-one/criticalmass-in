<?php

namespace Caldera\CriticalmassOpenweatherBundle\Utility\TemperatureConverter;

abstract class AbstractTemperatureConverter
{
    protected $celsius;
    protected $fahrenheit;

    public function getFahrenheit()
    {
        return $this->fahrenheit;
    }

    public function setFahrenheit($fahrenheit)
    {
        $this->fahrenheit = $fahrenheit;

        return $this;
    }

    public function getCelsius()
    {
        return $this->celsius;
    }

    public function setCelsius($celsius)
    {
        $this->celsius = $celsius;

        return $this;
    }

    abstract public function convert();
}