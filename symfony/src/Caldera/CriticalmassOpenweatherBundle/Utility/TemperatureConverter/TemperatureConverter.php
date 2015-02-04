<?php
/**
 * Created by IntelliJ IDEA.
 * User: malte
 * Date: 04.02.15
 * Time: 14:47
 */

namespace Caldera\CriticalmassOpenweatherBundle\Utility\TemperatureConverter;

class TemperatureConverter extends AbstractTemperatureConverter {

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