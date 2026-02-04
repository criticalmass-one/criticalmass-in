<?php declare(strict_types=1);

namespace Tests\Controller\Api\CityActivityApi;

use App\Entity\City;
use App\Entity\CityActivity;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class CityActivityApiTest extends AbstractApiControllerTestCase
{
    public function testCreateActivityScoreForCity(): void
    {
        $city = $this->entityManager->getRepository(City::class)->findOneBy(['city' => 'Hamburg']);
        $this->assertNotNull($city);
        $citySlug = $city->getMainSlugString();

        $activityData = [
            'score' => 0.87,
            'details' => [
                [
                    'signalType' => 'participation',
                    'rawCount' => 42,
                    'normalizedScore' => 0.85,
                    'weight' => 0.40,
                    'weightedScore' => 0.34,
                ],
                [
                    'signalType' => 'photo',
                    'rawCount' => 51,
                    'normalizedScore' => 1.0,
                    'weight' => 0.25,
                    'weightedScore' => 0.25,
                ],
                [
                    'signalType' => 'track',
                    'rawCount' => 9,
                    'normalizedScore' => 0.75,
                    'weight' => 0.20,
                    'weightedScore' => 0.15,
                ],
                [
                    'signalType' => 'social_feed',
                    'rawCount' => 30,
                    'normalizedScore' => 1.0,
                    'weight' => 0.15,
                    'weightedScore' => 0.15,
                ],
            ],
            'calculatedAt' => '2026-02-03T13:41:18+00:00',
        ];

        $this->client->request(
            'POST',
            sprintf('/api/city/%s/activity', $citySlug),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($activityData)
        );

        $this->assertResponseStatusCodeSame(201);

        $response = $this->getJsonResponse();

        $this->assertArrayHasKey('score', $response);
        $this->assertEquals(0.87, $response['score']);
        $this->assertArrayHasKey('participation_score', $response);
        $this->assertEquals(0.85, $response['participation_score']);
        $this->assertArrayHasKey('participation_raw_count', $response);
        $this->assertEquals(42, $response['participation_raw_count']);
    }

    public function testCreateActivityScoreUpdatesCityActivityScore(): void
    {
        $city = $this->entityManager->getRepository(City::class)->findOneBy(['city' => 'Kiel']);
        $this->assertNotNull($city);
        $citySlug = $city->getMainSlugString();

        $originalScore = $city->getActivityScore();
        $this->assertNull($originalScore, 'Kiel should have NULL activity score from fixtures');

        $newScore = 0.55;

        $activityData = [
            'score' => $newScore,
            'details' => [
                ['signalType' => 'participation', 'rawCount' => 20, 'normalizedScore' => 0.60, 'weight' => 0.40, 'weightedScore' => 0.24],
                ['signalType' => 'photo', 'rawCount' => 15, 'normalizedScore' => 0.70, 'weight' => 0.25, 'weightedScore' => 0.175],
                ['signalType' => 'track', 'rawCount' => 5, 'normalizedScore' => 0.50, 'weight' => 0.20, 'weightedScore' => 0.10],
                ['signalType' => 'social_feed', 'rawCount' => 10, 'normalizedScore' => 0.80, 'weight' => 0.15, 'weightedScore' => 0.12],
            ],
        ];

        $this->client->request(
            'POST',
            sprintf('/api/city/%s/activity', $citySlug),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($activityData)
        );

        $this->assertResponseStatusCodeSame(201);

        $this->entityManager->clear();

        $updatedCity = $this->entityManager->getRepository(City::class)->findOneBy(['city' => 'Kiel']);
        $this->assertEquals($newScore, $updatedCity->getActivityScore());
    }

    public function testCreateActivityScoreWithInvalidScore(): void
    {
        $city = $this->entityManager->getRepository(City::class)->findOneBy(['city' => 'Hamburg']);
        $citySlug = $city->getMainSlugString();

        $activityData = [
            'score' => 1.5,
            'details' => [
                ['signalType' => 'participation', 'rawCount' => 10, 'normalizedScore' => 0.5, 'weight' => 0.40, 'weightedScore' => 0.2],
                ['signalType' => 'photo', 'rawCount' => 10, 'normalizedScore' => 0.5, 'weight' => 0.25, 'weightedScore' => 0.125],
                ['signalType' => 'track', 'rawCount' => 10, 'normalizedScore' => 0.5, 'weight' => 0.20, 'weightedScore' => 0.1],
                ['signalType' => 'social_feed', 'rawCount' => 10, 'normalizedScore' => 0.5, 'weight' => 0.15, 'weightedScore' => 0.075],
            ],
        ];

        $this->client->request(
            'POST',
            sprintf('/api/city/%s/activity', $citySlug),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($activityData)
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testCreateActivityScoreWithMissingSignalType(): void
    {
        $city = $this->entityManager->getRepository(City::class)->findOneBy(['city' => 'Hamburg']);
        $citySlug = $city->getMainSlugString();

        $activityData = [
            'score' => 0.5,
            'details' => [
                ['signalType' => 'participation', 'rawCount' => 10, 'normalizedScore' => 0.5, 'weight' => 0.40, 'weightedScore' => 0.2],
                ['signalType' => 'photo', 'rawCount' => 10, 'normalizedScore' => 0.5, 'weight' => 0.25, 'weightedScore' => 0.125],
            ],
        ];

        $this->client->request(
            'POST',
            sprintf('/api/city/%s/activity', $citySlug),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($activityData)
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testCreateActivityScoreForNonExistentCity(): void
    {
        $activityData = [
            'score' => 0.5,
            'details' => [
                ['signalType' => 'participation', 'rawCount' => 10, 'normalizedScore' => 0.5, 'weight' => 0.40, 'weightedScore' => 0.2],
                ['signalType' => 'photo', 'rawCount' => 10, 'normalizedScore' => 0.5, 'weight' => 0.25, 'weightedScore' => 0.125],
                ['signalType' => 'track', 'rawCount' => 10, 'normalizedScore' => 0.5, 'weight' => 0.20, 'weightedScore' => 0.1],
                ['signalType' => 'social_feed', 'rawCount' => 10, 'normalizedScore' => 0.5, 'weight' => 0.15, 'weightedScore' => 0.075],
            ],
        ];

        $this->client->request(
            'POST',
            '/api/city/nonexistent-city-slug/activity',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($activityData)
        );

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCityActivityEntityIsPersisted(): void
    {
        $city = $this->entityManager->getRepository(City::class)->findOneBy(['city' => 'Berlin']);
        $citySlug = $city->getMainSlugString();

        $countBefore = count($this->entityManager->getRepository(CityActivity::class)->findBy(['city' => $city]));

        $activityData = [
            'score' => 0.75,
            'details' => [
                ['signalType' => 'participation', 'rawCount' => 30, 'normalizedScore' => 0.70, 'weight' => 0.40, 'weightedScore' => 0.28],
                ['signalType' => 'photo', 'rawCount' => 25, 'normalizedScore' => 0.80, 'weight' => 0.25, 'weightedScore' => 0.20],
                ['signalType' => 'track', 'rawCount' => 8, 'normalizedScore' => 0.65, 'weight' => 0.20, 'weightedScore' => 0.13],
                ['signalType' => 'social_feed', 'rawCount' => 20, 'normalizedScore' => 0.90, 'weight' => 0.15, 'weightedScore' => 0.135],
            ],
        ];

        $this->client->request(
            'POST',
            sprintf('/api/city/%s/activity', $citySlug),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($activityData)
        );

        $this->assertResponseStatusCodeSame(201);

        $this->entityManager->clear();

        $countAfter = count($this->entityManager->getRepository(CityActivity::class)->findBy(['city' => $city]));

        $this->assertEquals($countBefore + 1, $countAfter);
    }
}
