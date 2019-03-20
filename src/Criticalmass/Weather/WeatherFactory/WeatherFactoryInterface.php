<?php declare(strict_types=1);

namespace App\Criticalmass\Weather\WeatherFactory;

use App\Criticalmass\Weather\EntityInterface\WeatherInterface;
use Cmfcmf\OpenWeatherMap\Forecast;

interface WeatherFactoryInterface
{
    public function createWeather(Forecast $owmWeather): WeatherInterface;
}