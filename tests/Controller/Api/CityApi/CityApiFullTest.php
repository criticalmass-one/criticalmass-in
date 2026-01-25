<?php declare(strict_types=1);

namespace Tests\Controller\Api\CityApi;

use Tests\Controller\Api\AbstractApiControllerTestCase;

class CityApiFullTest extends AbstractApiControllerTestCase
{
    public function testListCities(): void
    {
        $this->client->request('GET', '/api/city');

        $this->assertResponseIsSuccessful();

        $cities = $this->getJsonResponseList();

        $this->assertNotEmpty($cities);
        $this->assertArrayHasKey('name', $cities[0]);
    }

    public function testListCitiesWithSize(): void
    {
        $this->client->request('GET', '/api/city', ['size' => 2]);

        $this->assertResponseIsSuccessful();

        $cities = $this->getJsonResponseList();

        $this->assertCount(2, $cities);
    }

    public function testShowCityHamburg(): void
    {
        $this->client->request('GET', '/api/hamburg');

        $this->assertResponseIsSuccessful();

        $city = $this->getJsonResponse();

        $this->assertEquals('Hamburg', $city['name']);
        $this->assertEquals('Critical Mass Hamburg', $city['title']);
        $this->assertEqualsWithDelta(53.5511, $city['latitude'], 0.01);
        $this->assertEqualsWithDelta(9.9937, $city['longitude'], 0.01);
    }

    public function testShowCityBerlin(): void
    {
        $this->client->request('GET', '/api/berlin');

        $this->assertResponseIsSuccessful();

        $city = $this->getJsonResponse();

        $this->assertEquals('Berlin', $city['name']);
    }

    public function testShowCityMunich(): void
    {
        $this->client->request('GET', '/api/munich');

        $this->assertResponseIsSuccessful();

        $city = $this->getJsonResponse();

        $this->assertEquals('Munich', $city['name']);
    }

    public function testShowCityKiel(): void
    {
        $this->client->request('GET', '/api/kiel');

        $this->assertResponseIsSuccessful();

        $city = $this->getJsonResponse();

        $this->assertEquals('Kiel', $city['name']);
    }

    public function testShowUnknownCityReturns404(): void
    {
        $this->client->request('GET', '/api/unknown-city');

        $this->assertResponseStatusCode(404);
    }

    public function testListCitiesOrderByPopulation(): void
    {
        $this->client->request('GET', '/api/city', [
            'orderBy' => 'cityPopulation',
            'orderDirection' => 'desc',
            'size' => 4,
        ]);

        $this->assertResponseIsSuccessful();

        $cities = $this->getJsonResponseList();

        $this->assertNotEmpty($cities);

        $populations = array_map(fn(array $city) => $city['city_population'], $cities);
        $sortedPopulations = $populations;
        rsort($sortedPopulations);

        $this->assertEquals($sortedPopulations, $populations);
    }

    public function testListCitiesWithRadiusQuery(): void
    {
        $this->client->request('GET', '/api/city', [
            'centerLatitude' => 53.5511,
            'centerLongitude' => 9.9937,
            'radius' => 100,
        ]);

        $this->assertResponseIsSuccessful();

        $cities = $this->getJsonResponseList();

        $this->assertNotEmpty($cities);

        $cityNames = array_map(fn(array $city) => $city['name'], $cities);
        $this->assertContains('Hamburg', $cityNames);
    }

    public function testListCitiesExtended(): void
    {
        $this->client->request('GET', '/api/city', ['extended' => true]);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
    }
}
