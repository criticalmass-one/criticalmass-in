<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use App\Entity\Ride;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class RideApiFullTest extends AbstractApiControllerTestCase
{
    public function testListRides(): void
    {
        $this->client->request('GET', '/api/ride');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response[0]);
    }

    public function testListRidesWithSize(): void
    {
        $this->client->request('GET', '/api/ride', ['size' => 3]);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertLessThanOrEqual(3, count($response));
    }

    public function testListRidesForCity(): void
    {
        $this->client->request('GET', '/api/ride', ['citySlug' => 'hamburg']);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);

        // Verify rides are in Hamburg area by coordinates instead of title
        foreach ($response as $ride) {
            // Hamburg coordinates: approx 53.55 N, 10.0 E
            $this->assertGreaterThan(53.4, $ride['latitude']);
            $this->assertLessThan(53.7, $ride['latitude']);
            $this->assertGreaterThan(9.8, $ride['longitude']);
            $this->assertLessThan(10.2, $ride['longitude']);
        }
    }

    public function testShowRideByDate(): void
    {
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();
        $this->assertNotEmpty($rides, 'No rides found in database');

        /** @var Ride $ride */
        $ride = $rides[0];
        $dateString = $ride->getDateTime()->format('Y-m-d');
        $citySlug = $ride->getCity()->getMainSlugString();

        $this->client->request('GET', sprintf('/api/%s/%s', $citySlug, $dateString));

        $this->assertResponseIsSuccessful();

        $responseRide = $this->getJsonResponse();

        $this->assertEquals($ride->getTitle(), $responseRide['title']);
    }

    public function testShowCurrentRide(): void
    {
        $this->client->request('GET', '/api/hamburg/current');

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertEquals(200, $statusCode);
    }

    public function testShowUnknownRideReturns404(): void
    {
        $this->client->request('GET', '/api/hamburg/1999-01-01');

        $this->assertResponseStatusCode(404);
    }

    public function testListRidesOrderByDateTime(): void
    {
        $this->client->request('GET', '/api/ride', [
            'orderBy' => 'dateTime',
            'orderDirection' => 'desc',
            'size' => 10,
        ]);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);

        $dates = array_map(fn(array $ride) => $ride['date_time'], $response);
        $sortedDates = $dates;
        rsort($sortedDates);

        $this->assertEquals($sortedDates, $dates);
    }

    public function testListRidesWithRadiusQuery(): void
    {
        $this->client->request('GET', '/api/ride', [
            'centerLatitude' => 53.5611,
            'centerLongitude' => 9.9895,
            'radius' => 50,
        ]);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
    }

    public function testListRidesExtended(): void
    {
        $this->client->request('GET', '/api/ride', ['extended' => true]);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
    }
}
