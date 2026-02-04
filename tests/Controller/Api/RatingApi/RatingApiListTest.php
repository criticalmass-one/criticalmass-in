<?php declare(strict_types=1);

namespace Tests\Controller\Api\RatingApi;

use App\DataFixtures\RideFixtures;
use App\Entity\Ride;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class RatingApiListTest extends AbstractApiControllerTestCase
{
    public function testListRatingsForRide(): void
    {
        $ride = $this->getTestRide();
        $citySlug = $ride->getCity()->getMainSlugString();
        $rideDate = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s/rating', $citySlug, $rideDate));

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
    }

    public function testListRatingsReturnsCorrectStructure(): void
    {
        $ride = $this->getTestRide();
        $citySlug = $ride->getCity()->getMainSlugString();
        $rideDate = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s/rating', $citySlug, $rideDate));

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        if (count($response) > 0) {
            $rating = $response[0];
            $this->assertArrayHasKey('id', $rating);
            $this->assertArrayHasKey('rating', $rating);
            $this->assertArrayHasKey('created_at', $rating);
        }
    }

    public function testListRatingsDoesNotExposeUserData(): void
    {
        $ride = $this->getTestRide();
        $citySlug = $ride->getCity()->getMainSlugString();
        $rideDate = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s/rating', $citySlug, $rideDate));

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        if (count($response) > 0) {
            $rating = $response[0];
            $this->assertArrayNotHasKey('user', $rating);
            $this->assertArrayNotHasKey('user_id', $rating);
        }
    }

    public function testListRatingsDoesNotExposeRideData(): void
    {
        $ride = $this->getTestRide();
        $citySlug = $ride->getCity()->getMainSlugString();
        $rideDate = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s/rating', $citySlug, $rideDate));

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        if (count($response) > 0) {
            $rating = $response[0];
            $this->assertArrayNotHasKey('ride', $rating);
            $this->assertArrayNotHasKey('ride_id', $rating);
        }
    }

    public function testListRatingsForNonExistentRideReturns404(): void
    {
        $this->client->request('GET', '/api/nonexistent-city/2099-12-31/rating');

        $this->assertResponseStatusCode(404);
    }

    public function testListRatingsForInvalidDateFormatReturns404(): void
    {
        $ride = $this->getTestRide();
        $citySlug = $ride->getCity()->getMainSlugString();

        $this->client->request('GET', sprintf('/api/%s/invalid-date/rating', $citySlug));

        $this->assertResponseStatusCode(404);
    }

    private function getTestRide(): Ride
    {
        $ride = $this->entityManager->getRepository(Ride::class)->findOneBy([]);
        $this->assertNotNull($ride, 'No ride found in database');

        return $ride;
    }
}
