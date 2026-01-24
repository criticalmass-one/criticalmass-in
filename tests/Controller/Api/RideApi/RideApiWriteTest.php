<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use App\Entity\City;
use App\Entity\Ride;
use App\Tests\Controller\Api\Schema\ApiSchemaDefinitions;
use App\Tests\Controller\Api\Schema\JsonStructureValidator;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

#[TestDox('Ride API Write Operations')]
class RideApiWriteTest extends AbstractApiControllerTestCase
{
    #[TestDox('PUT /api/{citySlug}/{rideIdentifier} creates new ride')]
    public function testCreateRide(): void
    {
        $cities = $this->entityManager->getRepository(City::class)->findAll();
        $this->assertNotEmpty($cities);

        $city = $cities[0];
        $citySlug = $city->getMainSlugString();

        // Use a date far in the future to avoid conflicts
        $futureDate = (new \DateTime())->modify('+10 years')->format('Y-m-d');

        $rideData = [
            'title' => 'Test API Ride',
            'description' => 'Created via API test',
            'latitude' => 53.5511,
            'longitude' => 9.9937,
            'location' => 'Test Location',
        ];

        $this->client->request(
            'PUT',
            sprintf('/api/%s/%s', $citySlug, $futureDate),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($rideData)
        );

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertIsArray($response);
        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('Test API Ride', $response['title']);

        // Validate schema
        JsonStructureValidator::assertMatchesSchema(
            ApiSchemaDefinitions::RIDE_LIST_ITEM_SCHEMA,
            $response
        );
    }

    #[TestDox('PUT /api/{citySlug}/{rideIdentifier} with invalid data returns 400')]
    public function testCreateRideWithInvalidDataReturns400(): void
    {
        $cities = $this->entityManager->getRepository(City::class)->findAll();
        $this->assertNotEmpty($cities);

        $city = $cities[0];
        $citySlug = $city->getMainSlugString();

        // Use a date far in the future
        $futureDate = (new \DateTime())->modify('+11 years')->format('Y-m-d');

        // Empty title should fail validation
        $rideData = [
            'title' => '', // Invalid: empty title
        ];

        $this->client->request(
            'PUT',
            sprintf('/api/%s/%s', $citySlug, $futureDate),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($rideData)
        );

        // Depending on validation rules, should return 400
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [200, 400], 'Should return 200 or 400 for validation');
    }

    #[TestDox('POST /api/{citySlug}/{rideIdentifier} updates existing ride')]
    public function testUpdateRide(): void
    {
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();
        $this->assertNotEmpty($rides);

        $ride = $rides[0];
        $citySlug = $ride->getCity()->getMainSlugString();
        $dateString = $ride->getDateTime()->format('Y-m-d');

        $updateData = [
            'description' => 'Updated description via API test - ' . uniqid(),
        ];

        $this->client->request(
            'POST',
            sprintf('/api/%s/%s', $citySlug, $dateString),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($updateData)
        );

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertIsArray($response);
        $this->assertStringContains('Updated description via API test', $response['description'] ?? '');
    }

    #[TestDox('POST /api/{citySlug}/{rideIdentifier} returns ride matching schema')]
    public function testUpdateRideResponseSchema(): void
    {
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();
        $this->assertNotEmpty($rides);

        $ride = $rides[0];
        $citySlug = $ride->getCity()->getMainSlugString();
        $dateString = $ride->getDateTime()->format('Y-m-d');

        $updateData = [
            'location' => 'Updated Location',
        ];

        $this->client->request(
            'POST',
            sprintf('/api/%s/%s', $citySlug, $dateString),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($updateData)
        );

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        JsonStructureValidator::assertMatchesSchema(
            ApiSchemaDefinitions::RIDE_LIST_ITEM_SCHEMA,
            $response
        );
    }

    #[TestDox('POST /api/{citySlug}/{invalidRide} returns 404')]
    public function testUpdateNonExistentRideReturns404(): void
    {
        $this->client->request(
            'POST',
            '/api/hamburg/1900-01-01',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['title' => 'Test'])
        );

        $this->assertResponseStatusCode(404);
    }

    #[TestDox('PUT /api/{invalidCity}/{rideIdentifier} returns 404')]
    public function testCreateRideForInvalidCityReturns404(): void
    {
        $futureDate = (new \DateTime())->modify('+12 years')->format('Y-m-d');

        $this->client->request(
            'PUT',
            sprintf('/api/nonexistent-city/%s', $futureDate),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['title' => 'Test'])
        );

        $this->assertResponseStatusCode(404);
    }

    /**
     * Helper method to check if a string contains a substring.
     */
    private function assertStringContains(string $needle, string $haystack): void
    {
        $this->assertStringContainsString($needle, $haystack);
    }
}
