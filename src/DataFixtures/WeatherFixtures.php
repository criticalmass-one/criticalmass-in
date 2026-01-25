<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Ride;
use App\Entity\Weather;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class WeatherFixtures extends Fixture implements DependentFixtureInterface
{
    public const HAMBURG_WEATHER_REFERENCE = 'weather-hamburg';
    public const BERLIN_WEATHER_REFERENCE = 'weather-berlin';
    public const MUNICH_WEATHER_REFERENCE = 'weather-munich';

    public function load(ObjectManager $manager): void
    {
        /** @var Ride $hamburgRidePast */
        $hamburgRidePast = $this->getReference(RideFixtures::HAMBURG_RIDE_PAST_REFERENCE, Ride::class);
        /** @var Ride $berlinRidePast */
        $berlinRidePast = $this->getReference(RideFixtures::BERLIN_RIDE_PAST_REFERENCE, Ride::class);
        /** @var Ride $munichRidePast */
        $munichRidePast = $this->getReference(RideFixtures::MUNICH_RIDE_PAST_REFERENCE, Ride::class);

        $hamburgWeather = $this->createWeather(
            $hamburgRidePast,
            18.5,
            22.3,
            'Clear',
            'clear sky',
            '01d',
            1015.0,
            65.0,
            3.5,
            180.0,
            10.0,
            0.0
        );
        $this->addReference(self::HAMBURG_WEATHER_REFERENCE, $hamburgWeather);
        $manager->persist($hamburgWeather);

        $berlinWeather = $this->createWeather(
            $berlinRidePast,
            16.0,
            21.5,
            'Clouds',
            'few clouds',
            '02d',
            1012.0,
            70.0,
            4.2,
            220.0,
            25.0,
            0.0
        );
        $this->addReference(self::BERLIN_WEATHER_REFERENCE, $berlinWeather);
        $manager->persist($berlinWeather);

        $munichWeather = $this->createWeather(
            $munichRidePast,
            14.0,
            19.8,
            'Rain',
            'light rain',
            '10d',
            1008.0,
            85.0,
            5.1,
            270.0,
            75.0,
            2.5
        );
        $this->addReference(self::MUNICH_WEATHER_REFERENCE, $munichWeather);
        $manager->persist($munichWeather);

        $manager->flush();
    }

    private function createWeather(
        Ride $ride,
        float $temperatureMin,
        float $temperatureMax,
        string $weather,
        string $weatherDescription,
        string $weatherIcon,
        float $pressure,
        float $humidity,
        float $windSpeed,
        float $windDirection,
        float $clouds,
        float $precipitation
    ): Weather {
        $temperatureDay = ($temperatureMin + $temperatureMax) / 2 + 2;
        $temperatureEvening = ($temperatureMin + $temperatureMax) / 2;
        $temperatureMorning = $temperatureMin + 2;
        $temperatureNight = $temperatureMin;

        return (new Weather())
            ->setRide($ride)
            ->setWeatherDateTime($ride->getDateTime())
            ->setTemperatureMin($temperatureMin)
            ->setTemperatureMax($temperatureMax)
            ->setTemperatureDay($temperatureDay)
            ->setTemperatureEvening($temperatureEvening)
            ->setTemperatureMorning($temperatureMorning)
            ->setTemperatureNight($temperatureNight)
            ->setWeather($weather)
            ->setWeatherDescription($weatherDescription)
            ->setWeatherIcon($weatherIcon)
            ->setPressure($pressure)
            ->setHumidity($humidity)
            ->setWindSpeed($windSpeed)
            ->setWindDirection($windDirection)
            ->setClouds($clouds)
            ->setPrecipitation($precipitation);
    }

    public function getDependencies(): array
    {
        return [
            RideFixtures::class,
        ];
    }
}
