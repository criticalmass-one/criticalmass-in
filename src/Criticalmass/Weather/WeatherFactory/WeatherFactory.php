<?php declare(strict_types=1);

namespace App\Criticalmass\Weather\WeatherFactory;

use App\Entity\Ride;
use App\Entity\Weather;
use Cmfcmf\OpenWeatherMap\Forecast;

class WeatherFactory implements WeatherFactoryInterface
{
    public function createWeather(Ride $ride, Forecast $owmWeather): Weather
    {
        $weather = new Weather();

        $weather
            ->setRide($ride)
            ->setCreationDateTime(new \DateTime())
            ->setWeatherDateTime($owmWeather->time->from)
            ->setJson(null)
            ->setTemperatureMin($owmWeather->temperature->min->getValue())
            ->setTemperatureMax($owmWeather->temperature->max->getValue())
            ->setTemperatureMorning($owmWeather->temperature->morning->getValue())
            ->setTemperatureDay($owmWeather->temperature->day->getValue())
            ->setTemperatureEvening($owmWeather->temperature->evening->getValue())
            ->setTemperatureNight($owmWeather->temperature->night->getValue())
            ->setWeather(null)
            ->setWeatherDescription($owmWeather->weather->description)
            ->setWeatherCode($owmWeather->weather->id)
            ->setWeatherIcon($owmWeather->weather->icon)
            ->setPressure($owmWeather->pressure->getValue())
            ->setHumidity($owmWeather->humidity->getValue())
            ->setWindSpeed($owmWeather->wind->speed->getValue())
            ->setWindDeg($owmWeather->wind->direction->getValue())
            ->setClouds($owmWeather->clouds->getValue())
            ->setRain($owmWeather->precipitation->getValue());

        return $weather;
    }
}