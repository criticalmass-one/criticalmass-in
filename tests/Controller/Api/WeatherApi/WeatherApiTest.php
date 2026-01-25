<?php declare(strict_types=1);

namespace Tests\Controller\Api\WeatherApi;

use App\Entity\Ride;
use App\Entity\Weather;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class WeatherApiTest extends AbstractApiControllerTestCase
{
    public function testAddWeatherToRide(): void
    {
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();
        $this->assertNotEmpty($rides, 'No rides found in database');

        /** @var Ride $ride */
        $ride = $rides[0];
        $dateString = $ride->getDateTime()->format('Y-m-d');
        $citySlug = $ride->getCity()->getMainSlugString();

        $weatherData = [
            'temperatureMin' => 15.0,
            'temperatureMax' => 22.5,
            'temperatureMorning' => 16.0,
            'temperatureDay' => 21.0,
            'temperatureEvening' => 19.5,
            'temperatureNight' => 15.5,
            'weather' => 'Clear',
            'weatherDescription' => 'clear sky',
            'weatherIcon' => '01d',
            'pressure' => 1015.0,
            'humidity' => 60.0,
            'windSpeed' => 3.5,
            'windDirection' => 180.0,
            'clouds' => 10.0,
            'precipitation' => 0.0,
        ];

        $this->client->request(
            'PUT',
            sprintf('/api/%s/%s/weather', $citySlug, $dateString),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($weatherData)
        );

        $this->assertResponseStatusCode(201);
    }

    public function testWeatherFixturesExist(): void
    {
        $weathers = $this->entityManager->getRepository(Weather::class)->findAll();

        $this->assertNotEmpty($weathers, 'Weather fixtures should be loaded');

        /** @var Weather $weather */
        $weather = $weathers[0];

        $this->assertNotNull($weather->getRide());
        $this->assertNotNull($weather->getTemperatureMin());
        $this->assertNotNull($weather->getTemperatureMax());
        $this->assertNotNull($weather->getWeather());
        $this->assertNotNull($weather->getWeatherDescription());
    }

    public function testWeatherHasCorrectRideAssociation(): void
    {
        $weathers = $this->entityManager->getRepository(Weather::class)->findAll();

        $this->assertNotEmpty($weathers);

        /** @var Weather $weather */
        foreach ($weathers as $weather) {
            $ride = $weather->getRide();
            $this->assertNotNull($ride);
            $this->assertNotNull($ride->getCity());
        }
    }

    public function testAddWeatherToUnknownRideReturns404(): void
    {
        $weatherData = [
            'temperatureMin' => 15.0,
            'temperatureMax' => 22.5,
            'weather' => 'Clear',
        ];

        $this->client->request(
            'PUT',
            '/api/hamburg/1999-01-01/weather',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($weatherData)
        );

        $this->assertResponseStatusCode(404);
    }
}
