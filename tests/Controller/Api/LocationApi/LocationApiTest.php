<?php declare(strict_types=1);

namespace Tests\Controller\Api\LocationApi;

use Tests\Controller\Api\AbstractApiControllerTestCase;

class LocationApiTest extends AbstractApiControllerTestCase
{
    public function testListLocationsForHamburg(): void
    {
        $this->client->request('GET', '/api/hamburg/location');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);

        $locationTitles = array_map(fn(array $location) => $location['title'], $response);
        $this->assertContains('Moorweide', $locationTitles);
    }

    public function testListLocationsForBerlin(): void
    {
        $this->client->request('GET', '/api/berlin/location');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);

        $locationTitles = array_map(fn(array $location) => $location['title'], $response);
        $this->assertContains('Heinrichplatz', $locationTitles);
        $this->assertContains('Brandenburger Tor', $locationTitles);
    }

    public function testShowLocationMoorweide(): void
    {
        $this->client->request('GET', '/api/hamburg/location/moorweide');

        $this->assertResponseIsSuccessful();

        $location = $this->getJsonResponse();

        $this->assertEquals('Moorweide', $location['title']);
        $this->assertEquals('moorweide', $location['slug']);
        $this->assertEqualsWithDelta(53.5611, $location['latitude'], 0.01);
        $this->assertEqualsWithDelta(9.9895, $location['longitude'], 0.01);
    }

    public function testShowLocationMarienplatz(): void
    {
        $this->client->request('GET', '/api/munich/location/marienplatz');

        $this->assertResponseIsSuccessful();

        $location = $this->getJsonResponse();

        $this->assertEquals('Marienplatz', $location['title']);
    }

    public function testShowUnknownLocationReturns404(): void
    {
        $this->client->request('GET', '/api/hamburg/location/unknown-location');

        $this->assertResponseStatusCode(404);
    }

    public function testListLocationsForUnknownCityReturns404(): void
    {
        $this->client->request('GET', '/api/unknown-city/location');

        $this->assertResponseStatusCode(404);
    }
}
