<?php declare(strict_types=1);

namespace App\Criticalmass\Weather\WeatherFactory;

use App\Criticalmass\Weather\EntityInterface\WeatherInterface;
use App\Entity\Weather;
use Cmfcmf\OpenWeatherMap\Forecast;

class WeatherFactory implements WeatherFactoryInterface
{
    protected function createEntity(): WeatherInterface
    {
        return new Weather();
    }

    protected function assignProperties(WeatherInterface $weather, Forecast $owmWeather): WeatherInterface
    {
        $reflection = new \ReflectionClass($weather);

        /** @var \ReflectionMethod $method */
        foreach ($reflection->getMethods() as $method) {
            $methodName = $method->getShortName();

            if (0 !== strpos($methodName, 'set')) {
                continue;
            }

            preg_match_all('/([A-Z][a-z]+)/', $methodName, $matches);

            $path = array_map('strtolower', $matches[0]);

            $weather = $this->assignProperty($weather, $owmWeather, $methodName, $path);
        }

        return $weather;
    }

    protected function assignProperty(WeatherInterface $weather, Forecast $owmWeather, string $methodName, array $path): WeatherInterface
    {
        if (2 !== count($path)) {
            return $weather;
        }

        list($prop1, $prop2) = $path;

        if (property_exists($owmWeather, $prop1) && property_exists($owmWeather->{$prop1}, $prop2) && is_object($owmWeather->{$prop1}->{$prop2})) {
            $weather->$methodName($owmWeather->{$prop1}->{$prop2}->getValue());
        }

        return $weather;
    }

    public function createWeather(Forecast $owmWeather): WeatherInterface
    {
        $weather = $this->createEntity();

        $weather = $this->assignProperties($weather, $owmWeather);

        $weather
            ->setCreationDateTime(new \DateTime())
            ->setWeatherDateTime($owmWeather->time->from)
            ->setWeather(null)
            ->setWeatherDescription($owmWeather->weather->description)
            ->setWeatherCode($owmWeather->weather->id)
            ->setWeatherIcon($owmWeather->weather->icon)
            ->setPressure($owmWeather->pressure->getValue())
            ->setHumidity($owmWeather->humidity->getValue())
            ->setWindDeg($owmWeather->wind->direction->getValue())
            ->setClouds($owmWeather->clouds->getValue())
            ->setRain($owmWeather->precipitation->getValue());

        return $weather;
    }
}