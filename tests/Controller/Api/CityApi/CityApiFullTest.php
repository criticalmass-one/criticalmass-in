<?php declare(strict_types=1);

namespace Tests\Controller\Api\CityApi;

use App\Entity\City;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class CityApiFullTest extends AbstractApiControllerTestCase
{
    public function testListCities(): void
    {
        $this->client->request('GET', '/api/city');

        $this->assertResponseIsSuccessful();

        $cities = $this->deserializeEntityList($this->client->getResponse()->getContent(), City::class);

        $this->assertNotEmpty($cities);
        $this->assertContainsOnlyInstancesOf(City::class, $cities);
    }

    public function testListCitiesWithSize(): void
    {
        $this->client->request('GET', '/api/city', ['size' => 2]);

        $this->assertResponseIsSuccessful();

        $cities = $this->deserializeEntityList($this->client->getResponse()->getContent(), City::class);

        $this->assertCount(2, $cities);
    }

    public function testShowCityHamburg(): void
    {
        $this->client->request('GET', '/api/hamburg');

        $this->assertResponseIsSuccessful();

        /** @var City $city */
        $city = $this->deserializeEntity($this->client->getResponse()->getContent(), City::class);

        $this->assertEquals('Hamburg', $city->getCity());
        $this->assertEquals('Critical Mass Hamburg', $city->getTitle());
        $this->assertEqualsWithDelta(53.5511, $city->getLatitude(), 0.01);
        $this->assertEqualsWithDelta(9.9937, $city->getLongitude(), 0.01);
    }

    public function testShowCityBerlin(): void
    {
        $this->client->request('GET', '/api/berlin');

        $this->assertResponseIsSuccessful();

        /** @var City $city */
        $city = $this->deserializeEntity($this->client->getResponse()->getContent(), City::class);

        $this->assertEquals('Berlin', $city->getCity());
    }

    public function testShowCityMunich(): void
    {
        $this->client->request('GET', '/api/munich');

        $this->assertResponseIsSuccessful();

        /** @var City $city */
        $city = $this->deserializeEntity($this->client->getResponse()->getContent(), City::class);

        $this->assertEquals('Munich', $city->getCity());
    }

    public function testShowCityKiel(): void
    {
        $this->client->request('GET', '/api/kiel');

        $this->assertResponseIsSuccessful();

        /** @var City $city */
        $city = $this->deserializeEntity($this->client->getResponse()->getContent(), City::class);

        $this->assertEquals('Kiel', $city->getCity());
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

        $cities = $this->deserializeEntityList($this->client->getResponse()->getContent(), City::class);

        $this->assertNotEmpty($cities);

        $populations = array_map(fn(City $city) => $city->getCityPopulation(), $cities);
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

        $cities = $this->deserializeEntityList($this->client->getResponse()->getContent(), City::class);

        $this->assertNotEmpty($cities);

        $cityNames = array_map(fn(City $city) => $city->getCity(), $cities);
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
