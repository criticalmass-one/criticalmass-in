<?php declare(strict_types=1);

namespace Tests\Controller\Api\RatingApi;

use App\Entity\Ride;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class RatingApiAverageTest extends AbstractApiControllerTestCase
{
    public function testAverageRatingEndpointReturnsSuccess(): void
    {
        $ride = $this->getTestRide();
        $citySlug = $ride->getCity()->getMainSlugString();
        $rideDate = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s/rating/average', $citySlug, $rideDate));

        $this->assertResponseIsSuccessful();
    }

    public function testAverageRatingReturnsCorrectStructure(): void
    {
        $ride = $this->getTestRide();
        $citySlug = $ride->getCity()->getMainSlugString();
        $rideDate = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s/rating/average', $citySlug, $rideDate));

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertArrayHasKey('average', $response);
        $this->assertArrayHasKey('count', $response);
    }

    public function testAverageRatingCountIsInteger(): void
    {
        $ride = $this->getTestRide();
        $citySlug = $ride->getCity()->getMainSlugString();
        $rideDate = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s/rating/average', $citySlug, $rideDate));

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsInt($response['count']);
        $this->assertGreaterThanOrEqual(0, $response['count']);
    }

    public function testAverageRatingIsNullOrFloat(): void
    {
        $ride = $this->getTestRide();
        $citySlug = $ride->getCity()->getMainSlugString();
        $rideDate = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s/rating/average', $citySlug, $rideDate));

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        if ($response['average'] !== null) {
            $this->assertIsFloat($response['average']);
            $this->assertGreaterThanOrEqual(1, $response['average']);
            $this->assertLessThanOrEqual(5, $response['average']);
        }
    }

    public function testAverageRatingForNonExistentRideReturns404(): void
    {
        $this->client->request('GET', '/api/nonexistent-city/2099-12-31/rating/average');

        $this->assertResponseStatusCode(404);
    }

    public function testAverageRatingCountMatchesListCount(): void
    {
        $ride = $this->getTestRide();
        $citySlug = $ride->getCity()->getMainSlugString();
        $rideDate = $ride->getDateTime()->format('Y-m-d');

        // Get average
        $this->client->request('GET', sprintf('/api/%s/%s/rating/average', $citySlug, $rideDate));
        $averageResponse = $this->getJsonResponse();

        // Get list
        $this->client->request('GET', sprintf('/api/%s/%s/rating', $citySlug, $rideDate));
        $listResponse = $this->getJsonResponse();

        $this->assertEquals(count($listResponse), $averageResponse['count']);
    }

    private function getTestRide(): Ride
    {
        $ride = $this->entityManager->getRepository(Ride::class)->findOneBy([]);
        $this->assertNotNull($ride, 'No ride found in database');

        return $ride;
    }
}
