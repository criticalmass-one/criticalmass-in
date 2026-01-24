<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use App\Entity\Ride;
use Tests\Controller\Api\Schema\ApiSchemaDefinitions;
use Tests\Controller\Api\Schema\JsonStructureValidator;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

#[TestDox('Ride API Schema Validation')]
class RideApiSchemaTest extends AbstractApiControllerTestCase
{
    #[TestDox('GET /api/ride returns array of rides matching RIDE_LIST_ITEM_SCHEMA')]
    public function testRideListResponseSchema(): void
    {
        $this->client->request('GET', '/api/ride');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response, 'Ride list should not be empty');

        foreach ($response as $index => $ride) {
            $this->assertIsArray($ride, "Ride at index {$index} should be an array");
            JsonStructureValidator::assertMatchesSchema(
                ApiSchemaDefinitions::RIDE_LIST_ITEM_SCHEMA,
                $ride,
                "rides[{$index}]"
            );
        }
    }

    #[TestDox('GET /api/{citySlug}/{rideIdentifier} returns ride matching RIDE_DETAIL_SCHEMA')]
    public function testRideDetailResponseSchema(): void
    {
        // Get a ride from the database to construct the URL
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();
        $this->assertNotEmpty($rides, 'No rides found in database');

        $ride = $rides[0];
        $citySlug = $ride->getCity()->getMainSlugString();
        $dateString = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s', $citySlug, $dateString));
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        JsonStructureValidator::assertMatchesSchema(
            ApiSchemaDefinitions::RIDE_DETAIL_SCHEMA,
            $response
        );
    }

    #[TestDox('Ride dateTime is a Unix timestamp (integer)')]
    public function testRideDateTimeIsUnixTimestamp(): void
    {
        $this->client->request('GET', '/api/ride?size=1');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertNotEmpty($response);

        $ride = $response[0];
        $this->assertArrayHasKey('datetime', $ride);
        $this->assertIsInt($ride['datetime'], 'dateTime should be a Unix timestamp (integer)');

        // Verify it's a reasonable timestamp (after year 2000, before year 2100)
        $this->assertGreaterThan(946684800, $ride['datetime'], 'Timestamp should be after year 2000');
        $this->assertLessThan(4102444800, $ride['datetime'], 'Timestamp should be before year 2100');
    }

    #[TestDox('Ride coordinates are valid when present')]
    public function testRideCoordinatesAreValidWhenPresent(): void
    {
        $this->client->request('GET', '/api/ride?size=10');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $ride) {
            if ($ride['latitude'] !== null) {
                $this->assertGreaterThanOrEqual(-90, $ride['latitude']);
                $this->assertLessThanOrEqual(90, $ride['latitude']);
            }
            if ($ride['longitude'] !== null) {
                $this->assertGreaterThanOrEqual(-180, $ride['longitude']);
                $this->assertLessThanOrEqual(180, $ride['longitude']);
            }
        }
    }

    #[TestDox('Ride with extended=true includes additional relations')]
    public function testRideExtendedIncludesRelations(): void
    {
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();
        $this->assertNotEmpty($rides);

        $ride = $rides[0];
        $citySlug = $ride->getCity()->getMainSlugString();
        $dateString = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s?extended=true', $citySlug, $dateString));
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        // Extended view should include city relation
        $this->assertArrayHasKey('city', $response, 'Extended ride should include city relation');
        $this->assertIsArray($response['city']);
    }

    #[TestDox('GET /api/{citySlug}/current returns current ride')]
    public function testCurrentRideEndpoint(): void
    {
        $this->client->request('GET', '/api/hamburg/current');

        // Either returns a ride or 404 if no current ride
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [200, 404], 'Should return 200 (ride found) or 404 (no current ride)');

        if ($statusCode === 200) {
            $response = $this->getJsonResponse();
            JsonStructureValidator::assertMatchesSchema(
                ApiSchemaDefinitions::RIDE_DETAIL_SCHEMA,
                $response
            );
        }
    }

    #[TestDox('Ride rideType is a valid enum value when present')]
    public function testRideTypeIsValidEnum(): void
    {
        $this->client->request('GET', '/api/ride?size=20');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $validRideTypes = ['critical_mass', 'kidical_mass', 'night_ride', 'demonstration', null];

        foreach ($response as $ride) {
            if (isset($ride['ridetype'])) {
                $this->assertContains(
                    $ride['ridetype'],
                    $validRideTypes,
                    sprintf("Invalid rideType: %s", $ride['ridetype'])
                );
            }
        }
    }

    #[TestDox('Ride enabled is a boolean')]
    public function testRideEnabledIsBoolean(): void
    {
        $this->client->request('GET', '/api/ride?size=1');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertNotEmpty($response);

        $this->assertIsBool($response[0]['enabled']);
    }

    #[TestDox('Ride participations numbers are non-negative integers')]
    public function testRideParticipationsAreNonNegativeIntegers(): void
    {
        $this->client->request('GET', '/api/ride?size=10');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $ride) {
            $this->assertIsInt($ride['participationsnumberyes']);
            $this->assertGreaterThanOrEqual(0, $ride['participationsnumberyes']);

            $this->assertIsInt($ride['participationsnumbermaybe']);
            $this->assertGreaterThanOrEqual(0, $ride['participationsnumbermaybe']);

            $this->assertIsInt($ride['participationsnumberno']);
            $this->assertGreaterThanOrEqual(0, $ride['participationsnumberno']);
        }
    }

    #[TestDox('GET /api/ride supports size parameter')]
    public function testRideListSizeParameter(): void
    {
        $this->client->request('GET', '/api/ride?size=3');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertLessThanOrEqual(3, count($response));
    }

    #[TestDox('GET /api/{citySlug}/{rideIdentifier}/listPhotos returns photo array')]
    public function testListPhotosEndpoint(): void
    {
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();
        $this->assertNotEmpty($rides);

        $ride = $rides[0];
        $citySlug = $ride->getCity()->getMainSlugString();
        $dateString = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s/listPhotos', $citySlug, $dateString));
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertIsArray($response);
    }

    #[TestDox('GET /api/{citySlug}/{rideIdentifier}/listTracks returns track array')]
    public function testListTracksEndpoint(): void
    {
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();
        $this->assertNotEmpty($rides);

        $ride = $rides[0];
        $citySlug = $ride->getCity()->getMainSlugString();
        $dateString = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s/listTracks', $citySlug, $dateString));
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertIsArray($response);
    }
}
