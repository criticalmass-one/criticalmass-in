<?php declare(strict_types=1);

namespace Tests\Controller\Api\WeatherApi;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Ride;
use App\Entity\Weather;
use Tests\Controller\Api\AbstractApiControllerTestCase;

/**
 * Tests der schreibenden Weather-Endpunkte (list/update/delete).
 * Transaktions-isoliert.
 */
class WeatherApiWriteTest extends AbstractApiControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $connection = $this->entityManager->getConnection();

        if ($connection->isTransactionActive()) {
            $connection->rollBack();
        }

        parent::tearDown();
    }

    private function createRideWithWeather(): array
    {
        $slug = 'weather-api-' . substr(md5(uniqid('', true)), 0, 12);

        $city = new City();
        $city->setCity('Wetterstadt');
        $city->setTitle('Critical Mass Wetterstadt');
        $city->setCreatedAt(new \DateTime());
        $this->entityManager->persist($city);

        $citySlug = new CitySlug();
        $citySlug->setSlug($slug);
        $citySlug->setCity($city);
        $this->entityManager->persist($citySlug);
        $city->setMainSlug($citySlug);

        $ride = new Ride();
        $ride->setCity($city);
        $ride->setDateTime(new \DateTime('2026-09-01 19:00:00'));
        $ride->setTitle('Critical Mass');
        $this->entityManager->persist($ride);

        $weather = new Weather();
        $weather->setRide($ride);
        $weather->setCreationDateTime(new \DateTime());
        $weather->setTemperatureMin(10.0);
        $weather->setTemperatureMax(20.0);
        $this->entityManager->persist($weather);

        $this->entityManager->flush();

        return [$city, $ride, $weather];
    }

    public function testListWeatherReturnsId(): void
    {
        [$city, , $weather] = $this->createRideWithWeather();

        $this->client->request('GET', '/api/' . $city->getMainSlugString() . '/2026-09-01/weather');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $ids = array_column($data, 'id');
        $this->assertContains($weather->getId(), $ids);
    }

    public function testUpdateWeather(): void
    {
        [, , $weather] = $this->createRideWithWeather();
        $weatherId = $weather->getId();

        $this->client->request('POST', '/api/weather/' . $weatherId, [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['temperatureMax' => 28.5]));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $updated = $this->entityManager->getRepository(Weather::class)->find($weatherId);
        $this->assertSame(28.5, $updated?->getTemperatureMax());
    }

    public function testDeleteWeather(): void
    {
        [, , $weather] = $this->createRideWithWeather();
        $weatherId = $weather->getId();

        $this->client->request('DELETE', '/api/weather/' . $weatherId);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $this->assertNull($this->entityManager->getRepository(Weather::class)->find($weatherId));
    }

    public function testDeleteUnknownWeatherReturns404(): void
    {
        $this->client->request('DELETE', '/api/weather/999999999');

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}
