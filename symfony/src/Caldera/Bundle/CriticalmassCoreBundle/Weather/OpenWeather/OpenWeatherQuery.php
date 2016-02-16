<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Weather\OpenWeather;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;

class OpenWeatherQuery
{
    /**
     * @var Ride $ride
     */
    protected $ride;
    
    public function setRide(Ride $ride)
    {
        $this->ride = $ride;
        
        return $this;
    }
    
    public function execute()
    {
        $jsonurl = 'http://api.openweathermap.org/data/2.5/forecast/daily?lat='.$this->ride->getLatitude().'&lon='.$this->ride->getLongitude().'&cnt=10&mode=json&units=metric&lang=de&appid=06d6a4917a689715462502cb56e3282b';
        $json = file_get_contents($jsonurl);
        
        return $json;
    }
}