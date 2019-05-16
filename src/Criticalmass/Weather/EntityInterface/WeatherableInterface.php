<?php declare(strict_types=1);

namespace App\Criticalmass\Weather\EntityInterface;

use Doctrine\Common\Collections\Collection;

interface WeatherableInterface
{
    public function addWeather(WeatherInterface $weather): WeatherableInterface;

    public function removeWeather(WeatherInterface $weathers): WeatherableInterface;

    public function getWeathers(): Collection;

    public function setWeathers(Collection $weathers): WeatherableInterface;
}
