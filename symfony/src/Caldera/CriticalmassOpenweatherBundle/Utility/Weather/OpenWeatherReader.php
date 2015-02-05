<?php
/**
 * Created by IntelliJ IDEA.
 * User: malte
 * Date: 04.02.15
 * Time: 15:40
 */

namespace Caldera\CriticalmassOpenweatherBundle\Utility\Weather;

use Caldera\CriticalmassOpenweatherBundle\Entity\Weather;

class OpenWeatherReader {
    
    protected $json;
    protected $date;
    protected $entity;
    
    public function setJson($json)
    {
        $this->json = $json;
    }
    
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    public function createEntity()
    {
        $this->entity = new Weather();
        
        $weather = json_decode($this->json);
        
        $dateTime = new \DateTime();
        
        $dayFound = false;
        
        foreach ($weather->list as $id => $weatherDay)
        {
            $dateTime->setTimestamp($weatherDay->dt);
        
            if ($this->date->format('Y-m-d') == $dateTime->format('Y-m-d'))
            {
                $dayFound = true;
                break;
            }
        }
        
        if (!$dayFound)
        {
            return null;
        }
        
        $weather = $weather->list[$id];

        $this->entity->setTemperatureMin($weather->temp->min);
        $this->entity->setTemperatureMax($weather->temp->max);
        $this->entity->setTemperatureMorning($weather->temp->morn);
        $this->entity->setTemperatureDay($weather->temp->day);
        $this->entity->setTemperatureEvening($weather->temp->eve);
        $this->entity->setTemperatureNight($weather->temp->night);

        $this->entity->setWeather($weather->weather[0]->main);
        $this->entity->setWeatherDescription($weather->weather[0]->description);
        $this->entity->setWeatherCode($weather->weather[0]->id);
        $this->entity->setWeatherIcon($weather->weather[0]->icon);

        $this->entity->setPressure($weather->pressure);
        $this->entity->setHumidity($weather->humidity);
        
        $this->entity->setWindSpeed($weather->speed);
        $this->entity->setWindDeg($weather->deg);

        $this->entity->setClouds($weather->clouds);
        $this->entity->setRain(isset($weather->rain) ?  $weather->rain : 0);

        return $this->entity;
    }
}