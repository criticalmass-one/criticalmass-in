<?php declare(strict_types=1);

namespace App\Criticalmass\Weather\EntityInterface;

interface WeatherInterface
{
    public function getWeatherDateTime(): ?\DateTime;
    public function setWeatherDateTime(\DateTime $weatherDateTime = null): WeatherInterface;

    public function getCreationDateTime(): ?\DateTime;
    public function setCreationDateTime(\DateTime $creationDateTime = null): WeatherInterface;

    public function getTemperatureMin(): ?float;
    public function setTemperatureMin(float $temperatureMin = null): WeatherInterface;

    public function getTemperatureMax(): ?float;
    public function setTemperatureMax(float $temperatureMax = null): WeatherInterface;

    public function getTemperatureMorning(): ?float;
    public function setTemperatureMorning(float $temperatureMorning = null): WeatherInterface;

    public function getTemperatureDay(): ?float;
    public function setTemperatureDay(float $temperatureDay = null): WeatherInterface;

    public function getTemperatureEvening(): ?float;
    public function setTemperatureEvening(float $temperatureEvening = null): WeatherInterface;

    public function getTemperatureNight(): ?float;
    public function setTemperatureNight(float $temperatureNight = null): WeatherInterface;

    public function getPressure(): ?float;
    public function setPressure(float $pressure = null): WeatherInterface;

    public function getHumidity(): ?float;
    public function setHumidity(float $humidity = null): WeatherInterface;

    public function getWeatherCode(): ?int;
    public function setWeatherCode(int $weatherCode = null): WeatherInterface;

    public function getWeather(): ?string;
    public function setWeather(string $weather = null): WeatherInterface;

    public function getWeatherDescription(): ?string;
    public function setWeatherDescription(string $weatherDescription = null): WeatherInterface;

    public function getWindSpeed(): ?float;
    public function setWindSpeed(float $windSpeed = null): WeatherInterface;

    public function getWindDirection(): ?float;
    public function setWindDirection(float $windDirection = null): WeatherInterface;

    public function getClouds(): ?float;
    public function setClouds(float $clouds = null): WeatherInterface;

    public function getPrecipitation(): ?float;
    public function setPrecipitation(float $precipitation = null): WeatherInterface;

    public function getWeatherIcon(): ?string;
    public function setWeatherIcon(string $weatherIcon = null): WeatherInterface;
}
