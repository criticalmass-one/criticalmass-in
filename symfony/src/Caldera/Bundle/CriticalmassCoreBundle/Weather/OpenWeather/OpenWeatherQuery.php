<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Weather\OpenWeather;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;

class OpenWeatherQuery
{
    /**
     * @var Ride $ride
     */
    protected $ride;

    protected $appId;

    public function __construct($appId)
    {
        $this->appId = $appId;
    }

    public function setRide(Ride $ride)
    {
        $this->ride = $ride;
        
        return $this;
    }

    protected function getCoords()
    {
        $coords = [];

        if ($this->ride->getHasLocation()) {
            $coords = [
                'latitude' => $this->ride->getLatitude(),
                'longitude' => $this->ride->getLongitude()
            ];
        } else {
            $coords = [
                'latitude' => $this->ride->getCity()->getLatitude(),
                'longitude' => $this->ride->getCity()->getLongitude()
            ];
        }

        return $coords;
    }
    
    public function execute()
    {
        $coords = $this->getCoords();

        $jsonurl = 'http://api.openweathermap.org/data/2.5/forecast/daily?lat='.$coords['latitude'].'&lon='.$coords['longitude'].'&cnt=10&mode=json&units=metric&lang=de&appid='.$this->appId;
        $json = file_get_contents($jsonurl);
        
        return $json;
    }
}