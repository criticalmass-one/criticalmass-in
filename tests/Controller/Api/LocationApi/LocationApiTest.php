<?php declare(strict_types=1);

namespace Tests\Controller\Api\LocationApi;

use App\Entity\Location;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class LocationApiTest extends AbstractApiControllerTestCase
{
    public function testListLocationsForHamburg(): void
    {
        $this->client->request('GET', '/api/hamburg/location');

        $this->assertResponseIsSuccessful();

        $locations = $this->deserializeEntityList($this->client->getResponse()->getContent(), Location::class);

        $this->assertNotEmpty($locations);
        $this->assertContainsOnlyInstancesOf(Location::class, $locations);

        $locationTitles = array_map(fn(Location $location) => $location->getTitle(), $locations);
        $this->assertContains('Moorweide', $locationTitles);
    }

    public function testListLocationsForBerlin(): void
    {
        $this->client->request('GET', '/api/berlin/location');

        $this->assertResponseIsSuccessful();

        $locations = $this->deserializeEntityList($this->client->getResponse()->getContent(), Location::class);

        $this->assertNotEmpty($locations);

        $locationTitles = array_map(fn(Location $location) => $location->getTitle(), $locations);
        $this->assertContains('Heinrichplatz', $locationTitles);
        $this->assertContains('Brandenburger Tor', $locationTitles);
    }

    public function testShowLocationMoorweide(): void
    {
        $this->client->request('GET', '/api/hamburg/location/moorweide');

        $this->assertResponseIsSuccessful();

        /** @var Location $location */
        $location = $this->deserializeEntity($this->client->getResponse()->getContent(), Location::class);

        $this->assertEquals('Moorweide', $location->getTitle());
        $this->assertEquals('moorweide', $location->getSlug());
        $this->assertEqualsWithDelta(53.5611, $location->getLatitude(), 0.01);
        $this->assertEqualsWithDelta(9.9895, $location->getLongitude(), 0.01);
    }

    public function testShowLocationMarienplatz(): void
    {
        $this->client->request('GET', '/api/munich/location/marienplatz');

        $this->assertResponseIsSuccessful();

        /** @var Location $location */
        $location = $this->deserializeEntity($this->client->getResponse()->getContent(), Location::class);

        $this->assertEquals('Marienplatz', $location->getTitle());
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
