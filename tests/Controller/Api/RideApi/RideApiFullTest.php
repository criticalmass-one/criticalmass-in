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

        $rides = $this->deserializeEntityList($this->client->getResponse()->getContent(), Ride::class);

        $this->assertNotEmpty($rides);
        $this->assertContainsOnlyInstancesOf(Ride::class, $rides);
    }

    public function testListRidesWithSize(): void
    {
        $this->client->request('GET', '/api/ride', ['size' => 3]);

        $this->assertResponseIsSuccessful();

        $rides = $this->deserializeEntityList($this->client->getResponse()->getContent(), Ride::class);

        $this->assertLessThanOrEqual(3, count($rides));
    }

    public function testListRidesForCity(): void
    {
        $this->client->request('GET', '/api/ride', ['citySlug' => 'hamburg']);

        $this->assertResponseIsSuccessful();

        $rides = $this->deserializeEntityList($this->client->getResponse()->getContent(), Ride::class);

        $this->assertNotEmpty($rides);

        foreach ($rides as $ride) {
            $this->assertStringContainsString('Hamburg', $ride->getTitle());
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

        /** @var Ride $responseRide */
        $responseRide = $this->deserializeEntity($this->client->getResponse()->getContent(), Ride::class);

        $this->assertEquals($ride->getTitle(), $responseRide->getTitle());
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

        $rides = $this->deserializeEntityList($this->client->getResponse()->getContent(), Ride::class);

        $this->assertNotEmpty($rides);

        $dates = array_map(fn(Ride $ride) => $ride->getDateTime()->getTimestamp(), $rides);
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

        $rides = $this->deserializeEntityList($this->client->getResponse()->getContent(), Ride::class);

        $this->assertNotEmpty($rides);
    }

    public function testListRidesExtended(): void
    {
        $this->client->request('GET', '/api/ride', ['extended' => true]);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
    }
}
