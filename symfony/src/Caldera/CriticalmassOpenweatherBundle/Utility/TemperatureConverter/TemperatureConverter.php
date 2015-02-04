<?php
/**
 * Created by IntelliJ IDEA.
 * User: malte
 * Date: 04.02.15
 * Time: 14:47
 */

namespace Caldera\CriticalmassOpenweatherBundle\Utility\TemperatureConverter;

class TemperatureConverter {
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

    public function convert()
    {
        if ($this->celsius)
        {
            $this->fahrenheit = $this->celsius * 1.8 + 32;
        }
        else
        {
            $this->celsius = ($this->fahrenheit - 32) * (5 / 9);
        }
        
        return $this;
    }
}