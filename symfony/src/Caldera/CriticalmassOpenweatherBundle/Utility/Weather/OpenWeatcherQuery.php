<?php
/**
 * Created by IntelliJ IDEA.
 * User: malte
 * Date: 04.02.15
 * Time: 13:54
 */

namespace Caldera\CriticalmassOpenweatherBundle\Utility\Weather;

use Caldera\CriticalmassCoreBundle\Entity\Ride;

class OpenWeatcherQuery {
    protected $ride;
    
    public function setRide(Ride $ride)
    {
        $this->ride = $ride;
        
        return $this;
    }
    
    public function execute()
    {
        $jsonurl = 'http://api.openweathermap.org/data/2.5/forecast/daily?lat='.$this->ride->getLatitude().'&lon='.$this->ride->getLongitude().'&cnt=10&mode=json&units=metric&lang=de';
        $json = file_get_contents($jsonurl);
        
        return $json;
    }
}