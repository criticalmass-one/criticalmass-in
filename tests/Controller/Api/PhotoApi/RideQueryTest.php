<?php declare(strict_types=1);

namespace Tests\Controller\Api\PhotoApi;

use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class RideQueryTest extends AbstractApiControllerTestCase
{
    #[TestDox('Querying for the past Hamburg ride will only return its photos.')]
    public function testPhotoListWithRideQueryForHamburg(): void
    {
        $rideId = $this->queryMostRecentPastRideId('hamburg');

        $this->client->request('GET', sprintf('/api/photo?citySlug=hamburg&rideIdentifier=%d', $rideId));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response = $this->getJsonResponse();

        // Verify we get an array of photos
        $this->assertIsArray($response);
        $this->assertNotEmpty($response, 'Should have photos for Hamburg ride');

        // Verify each item has expected photo properties
        foreach ($response as $photo) {
            $this->assertArrayHasKey('id', $photo);
        }
    }

    #[TestDox('Querying for the past Berlin ride will only return its photos.')]
    public function testPhotoListWithRideQueryForBerlin(): void
    {
        $rideId = $this->queryMostRecentPastRideId('berlin');

        $this->client->request('GET', sprintf('/api/photo?citySlug=berlin&rideIdentifier=%d', $rideId));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response = $this->getJsonResponse();

        // Verify we get an array of photos
        $this->assertIsArray($response);
        $this->assertNotEmpty($response, 'Should have photos for Berlin ride');

        // Verify each item has expected photo properties
        foreach ($response as $photo) {
            $this->assertArrayHasKey('id', $photo);
        }
    }

    #[TestDox('Querying for a non existent slug for city and ride returns 404 not found.')]
    public function testPhotoListWithCityQueryForNonExistentCity(): void
    {
        $this->client->request('GET', '/api/photo?citySlug=foobarcity&rideIdentifier=1245');

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * The photo fixtures are attached to the most recent past ride of a city.
     */
    private function queryMostRecentPastRideId(string $citySlug): int
    {
        $this->client->request('GET', '/api/ride?citySlug=' . $citySlug);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $rideList = $this->getJsonResponse();

        $pastRideList = array_filter($rideList, fn(array $ride): bool => $ride['date_time'] < time());
        $this->assertNotEmpty($pastRideList, sprintf('Should have a past ride for %s', $citySlug));

        usort($pastRideList, fn(array $a, array $b): int => $b['date_time'] <=> $a['date_time']);

        return $pastRideList[0]['id'];
    }
}
