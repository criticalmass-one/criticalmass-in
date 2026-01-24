<?php declare(strict_types=1);

namespace Tests\Controller\Api\SubrideApi;

use App\Entity\Ride;
use App\Entity\Subride;
use App\Tests\Controller\Api\Schema\ApiSchemaDefinitions;
use App\Tests\Controller\Api\Schema\JsonStructureValidator;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

#[TestDox('Subride API Tests')]
class SubrideApiTest extends AbstractApiControllerTestCase
{
    #[TestDox('GET /api/{citySlug}/{rideIdentifier}/subride returns array')]
    public function testSubrideListReturnsArray(): void
    {
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();
        $this->assertNotEmpty($rides, 'No rides found in database');

        $ride = $rides[0];
        $citySlug = $ride->getCity()->getMainSlugString();
        $dateString = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s/subride', $citySlug, $dateString));
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertIsArray($response);
    }

    #[TestDox('GET /api/{citySlug}/{rideIdentifier}/subride returns subrides matching SUBRIDE_SCHEMA when present')]
    public function testSubrideListResponseSchema(): void
    {
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();
        $this->assertNotEmpty($rides);

        $ride = $rides[0];
        $citySlug = $ride->getCity()->getMainSlugString();
        $dateString = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s/subride', $citySlug, $dateString));
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        // Skip schema validation if no subrides exist
        if (empty($response)) {
            $this->markTestSkipped('No subrides found for this ride');
        }

        foreach ($response as $index => $subride) {
            $this->assertIsArray($subride, "Subride at index {$index} should be an array");
            JsonStructureValidator::assertMatchesSchema(
                ApiSchemaDefinitions::SUBRIDE_SCHEMA,
                $subride,
                "subrides[{$index}]"
            );
        }
    }

    #[TestDox('GET /api/{citySlug}/{rideIdentifier}/{id} returns subride matching schema')]
    public function testSubrideDetailResponseSchema(): void
    {
        $subrides = $this->entityManager->getRepository(Subride::class)->findAll();

        if (empty($subrides)) {
            $this->markTestSkipped('No subrides found in database');
        }

        $subride = $subrides[0];
        $ride = $subride->getRide();
        $citySlug = $ride->getCity()->getMainSlugString();
        $dateString = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s/%d', $citySlug, $dateString, $subride->getId()));
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        JsonStructureValidator::assertMatchesSchema(
            ApiSchemaDefinitions::SUBRIDE_SCHEMA,
            $response
        );
    }

    #[TestDox('GET /api/{invalidCity}/{rideIdentifier}/subride returns 404')]
    public function testSubrideListForInvalidCityReturns404(): void
    {
        $this->client->request('GET', '/api/nonexistent-city/2023-06-30/subride');
        $this->assertResponseStatusCode(404);
    }

    #[TestDox('GET /api/{citySlug}/{invalidRide}/subride returns 404')]
    public function testSubrideListForInvalidRideReturns404(): void
    {
        $this->client->request('GET', '/api/hamburg/1900-01-01/subride');
        $this->assertResponseStatusCode(404);
    }

    #[TestDox('Subride timestamp is a Unix timestamp')]
    public function testSubrideTimestampIsUnixTimestamp(): void
    {
        $subrides = $this->entityManager->getRepository(Subride::class)->findAll();

        if (empty($subrides)) {
            $this->markTestSkipped('No subrides found in database');
        }

        $subride = $subrides[0];
        $ride = $subride->getRide();
        $citySlug = $ride->getCity()->getMainSlugString();
        $dateString = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s/%d', $citySlug, $dateString, $subride->getId()));
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertArrayHasKey('timestamp', $response);
        $this->assertIsInt($response['timestamp'], 'timestamp should be a Unix timestamp (integer)');
    }

    #[TestDox('Subride coordinates are valid when present')]
    public function testSubrideCoordinatesAreValidWhenPresent(): void
    {
        $subrides = $this->entityManager->getRepository(Subride::class)->findAll();

        if (empty($subrides)) {
            $this->markTestSkipped('No subrides found in database');
        }

        $subride = $subrides[0];
        $ride = $subride->getRide();
        $citySlug = $ride->getCity()->getMainSlugString();
        $dateString = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s/%d', $citySlug, $dateString, $subride->getId()));
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        if (isset($response['latitude']) && $response['latitude'] !== null) {
            $this->assertGreaterThanOrEqual(-90, $response['latitude']);
            $this->assertLessThanOrEqual(90, $response['latitude']);
        }
        if (isset($response['longitude']) && $response['longitude'] !== null) {
            $this->assertGreaterThanOrEqual(-180, $response['longitude']);
            $this->assertLessThanOrEqual(180, $response['longitude']);
        }
    }
}
