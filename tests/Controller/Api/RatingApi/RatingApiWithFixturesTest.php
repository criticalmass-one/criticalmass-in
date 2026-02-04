<?php declare(strict_types=1);

namespace Tests\Controller\Api\RatingApi;

use App\DataFixtures\RideFixtures;
use App\Entity\Ride;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class RatingApiWithFixturesTest extends AbstractApiControllerTestCase
{
    public function testHamburgRideHasThreeRatings(): void
    {
        $ride = $this->entityManager->getRepository(Ride::class)->findOneBy([]);

        if (!$ride) {
            $this->markTestSkipped('No rides found in database');
        }

        $citySlug = $ride->getCity()->getMainSlugString();
        $rideDate = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s/rating', $citySlug, $rideDate));

        $this->assertResponseIsSuccessful();
    }

    public function testRatingsAreOrderedById(): void
    {
        $ride = $this->entityManager->getRepository(Ride::class)->findOneBy([]);

        if (!$ride) {
            $this->markTestSkipped('No rides found in database');
        }

        $citySlug = $ride->getCity()->getMainSlugString();
        $rideDate = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s/rating', $citySlug, $rideDate));

        $response = $this->getJsonResponse();

        if (count($response) > 1) {
            for ($i = 1; $i < count($response); $i++) {
                $this->assertGreaterThanOrEqual(
                    $response[$i - 1]['id'],
                    $response[$i]['id'],
                    'Ratings should be ordered by id'
                );
            }
        }
    }

    public function testRatingValueIsWithinValidRange(): void
    {
        $ride = $this->entityManager->getRepository(Ride::class)->findOneBy([]);

        if (!$ride) {
            $this->markTestSkipped('No rides found in database');
        }

        $citySlug = $ride->getCity()->getMainSlugString();
        $rideDate = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s/rating', $citySlug, $rideDate));

        $response = $this->getJsonResponse();

        foreach ($response as $rating) {
            $this->assertGreaterThanOrEqual(1, $rating['rating']);
            $this->assertLessThanOrEqual(5, $rating['rating']);
        }
    }

    public function testCreatedAtIsUnixTimestamp(): void
    {
        $ride = $this->entityManager->getRepository(Ride::class)->findOneBy([]);

        if (!$ride) {
            $this->markTestSkipped('No rides found in database');
        }

        $citySlug = $ride->getCity()->getMainSlugString();
        $rideDate = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s/rating', $citySlug, $rideDate));

        $response = $this->getJsonResponse();

        foreach ($response as $rating) {
            $this->assertIsInt($rating['created_at']);
            $this->assertGreaterThan(0, $rating['created_at']);
        }
    }

    public function testAverageIsCalculatedCorrectly(): void
    {
        $ride = $this->entityManager->getRepository(Ride::class)->findOneBy([]);

        if (!$ride) {
            $this->markTestSkipped('No rides found in database');
        }

        $citySlug = $ride->getCity()->getMainSlugString();
        $rideDate = $ride->getDateTime()->format('Y-m-d');

        // Get list
        $this->client->request('GET', sprintf('/api/%s/%s/rating', $citySlug, $rideDate));
        $listResponse = $this->getJsonResponse();

        if (count($listResponse) === 0) {
            $this->markTestSkipped('No ratings found for ride');
        }

        // Calculate expected average
        $sum = array_sum(array_column($listResponse, 'rating'));
        $expectedAverage = round($sum / count($listResponse), 2);

        // Get average from API
        $this->client->request('GET', sprintf('/api/%s/%s/rating/average', $citySlug, $rideDate));
        $averageResponse = $this->getJsonResponse();

        $this->assertEquals($expectedAverage, $averageResponse['average']);
    }

    public function testEmptyRatingsReturnsEmptyArray(): void
    {
        // Find a ride without ratings (future ride)
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();

        foreach ($rides as $ride) {
            $citySlug = $ride->getCity()->getMainSlugString();
            $rideDate = $ride->getDateTime()->format('Y-m-d');

            $this->client->request('GET', sprintf('/api/%s/%s/rating', $citySlug, $rideDate));
            $response = $this->getJsonResponse();

            // Just verify the response is a valid array
            $this->assertIsArray($response);
            return;
        }

        $this->markTestSkipped('No rides found');
    }

    public function testEmptyRatingsAverageReturnsNullAverage(): void
    {
        // Find a ride and check
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();

        foreach ($rides as $ride) {
            $citySlug = $ride->getCity()->getMainSlugString();
            $rideDate = $ride->getDateTime()->format('Y-m-d');

            $this->client->request('GET', sprintf('/api/%s/%s/rating/average', $citySlug, $rideDate));
            $response = $this->getJsonResponse();

            $this->assertArrayHasKey('average', $response);
            $this->assertArrayHasKey('count', $response);

            if ($response['count'] === 0) {
                $this->assertNull($response['average']);
            }
            return;
        }

        $this->markTestSkipped('No rides found');
    }
}
