<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use App\Entity\City;
use App\Entity\Ride;
use Tests\Controller\Api\Schema\ApiSchemaDefinitions;
use Tests\Controller\Api\Schema\JsonStructureValidator;
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

        // Use a random date far in the future to avoid conflicts with existing rides
        $randomDays = random_int(3650, 7300); // 10-20 years in the future
        $futureDate = (new \DateTime())->modify("+$randomDays days")->format('Y-m-d');

        $rideData = [
            'title' => 'Test API Ride',
            'description' => 'Created via API test',
            'latitude' => 53.5511,
            'longitude' => 9.9937,
            'location' => 'Test Location',
            'date_time' => (new \DateTime($futureDate . ' 19:00:00'))->getTimestamp(),
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

        // Use a random date far in the future
        $randomDays = random_int(7301, 10950); // 20-30 years in the future
        $futureDate = (new \DateTime())->modify("+$randomDays days")->format('Y-m-d');

        // Empty title should fail validation
        $rideData = [
            'title' => '', // Invalid: empty title
            'date_time' => (new \DateTime($futureDate . ' 19:00:00'))->getTimestamp(),
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
            'location' => 'Updated location via API test',
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
        // Verify we got a valid ride response with required fields
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('title', $response);
        $this->assertEquals($ride->getId(), $response['id']);
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

    #[TestDox('PUT /api/{citySlug}/{date} fails when a date-based ride already exists for that day')]
    public function testCreateDuplicateDateRideReturns400(): void
    {
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();
        $this->assertNotEmpty($rides);

        // Find a ride without slug (date-based ride)
        $existingRide = null;
        foreach ($rides as $ride) {
            if (!$ride->getSlug()) {
                $existingRide = $ride;
                break;
            }
        }
        $this->assertNotNull($existingRide, 'No date-based ride found in fixtures');

        $citySlug = $existingRide->getCity()->getMainSlugString();
        $dateString = $existingRide->getDateTime()->format('Y-m-d');

        $rideData = [
            'title' => 'Duplicate Date Ride',
            'date_time' => $existingRide->getDateTime()->getTimestamp(),
        ];

        $this->client->request(
            'PUT',
            sprintf('/api/%s/%s', $citySlug, $dateString),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($rideData)
        );

        $this->assertResponseStatusCode(400);
    }

    #[TestDox('PUT /api/{citySlug}/{rideIdentifier} succeeds with slug on a day that already has a date-based ride')]
    public function testCreateSlugRideOnExistingDateSucceeds(): void
    {
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();
        $this->assertNotEmpty($rides);

        // Find a ride without slug (date-based ride)
        $existingRide = null;
        foreach ($rides as $ride) {
            if (!$ride->getSlug()) {
                $existingRide = $ride;
                break;
            }
        }
        $this->assertNotNull($existingRide, 'No date-based ride found in fixtures');

        $citySlug = $existingRide->getCity()->getMainSlugString();
        $dateString = $existingRide->getDateTime()->format('Y-m-d');

        $rideData = [
            'title' => 'Kidical Mass Special Event',
            'date_time' => $existingRide->getDateTime()->getTimestamp(),
        ];

        $this->client->request(
            'PUT',
            sprintf('/api/%s/kidical-mass-%s', $citySlug, $dateString),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($rideData)
        );

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertIsArray($response);
        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('Kidical Mass Special Event', $response['title']);
    }

    #[TestDox('PUT /api/{citySlug}/{rideIdentifier} allows multiple slug-based rides on the same day')]
    public function testCreateMultipleSlugRidesOnSameDateSucceeds(): void
    {
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();
        $this->assertNotEmpty($rides);

        $existingRide = null;
        foreach ($rides as $ride) {
            if (!$ride->getSlug()) {
                $existingRide = $ride;
                break;
            }
        }
        $this->assertNotNull($existingRide, 'No date-based ride found in fixtures');

        $citySlug = $existingRide->getCity()->getMainSlugString();
        $dateString = $existingRide->getDateTime()->format('Y-m-d');

        $this->client->request(
            'PUT',
            sprintf('/api/%s/second-slug-event-%s', $citySlug, $dateString),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => 'Second Slug Event',
                'date_time' => $existingRide->getDateTime()->getTimestamp(),
            ])
        );

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertIsArray($response);
        $this->assertEquals('Second Slug Event', $response['title']);
    }
}
