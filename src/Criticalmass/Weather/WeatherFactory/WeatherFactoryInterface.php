<?php declare(strict_types=1);

namespace App\Criticalmass\Weather\WeatherFactory;

use App\Entity\Ride;
use App\Entity\Weather;
use Cmfcmf\OpenWeatherMap\Forecast;

interface WeatherFactoryInterface
{
    public function createWeather(Ride $ride, Forecast $owmWeather): Weather;
}