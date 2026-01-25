<?php declare(strict_types=1);

namespace Tests\Controller\Api\TrackApi;

use App\Entity\Track;
use Tests\Controller\Api\Schema\ApiSchemaDefinitions;
use Tests\Controller\Api\Schema\JsonStructureValidator;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

#[TestDox('Track API Schema Validation')]
class TrackApiSchemaTest extends AbstractApiControllerTestCase
{
    #[TestDox('GET /api/track returns array of tracks matching TRACK_PUBLIC_SCHEMA')]
    public function testTrackListResponseSchema(): void
    {
        $this->client->request('GET', '/api/track');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);

        // Skip if no tracks available
        if (empty($response)) {
            $this->markTestSkipped('No tracks found in database');
        }

        foreach ($response as $index => $track) {
            $this->assertIsArray($track, "Track at index {$index} should be an array");
            JsonStructureValidator::assertMatchesSchema(
                ApiSchemaDefinitions::TRACK_PUBLIC_SCHEMA,
                $track,
                "tracks[{$index}]"
            );
        }
    }

    #[TestDox('GET /api/track/{trackId} returns track matching schema')]
    public function testTrackDetailResponseSchema(): void
    {
        $tracks = $this->entityManager->getRepository(Track::class)->findAll();

        if (empty($tracks)) {
            $this->markTestSkipped('No tracks found in database');
        }

        $track = $tracks[0];
        $this->client->request('GET', sprintf('/api/track/%d', $track->getId()));
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        JsonStructureValidator::assertMatchesSchema(
            ApiSchemaDefinitions::TRACK_PUBLIC_SCHEMA,
            $response
        );
    }

    #[TestDox('Track creationDateTime is a Unix timestamp')]
    public function testTrackCreationDateTimeIsUnixTimestamp(): void
    {
        $this->client->request('GET', '/api/track?size=1');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        if (empty($response)) {
            $this->markTestSkipped('No tracks found');
        }

        $track = $response[0];
        $this->assertArrayHasKey('creation_date_time', $track);
        $this->assertIsInt($track['creation_date_time'], 'creationDateTime should be a Unix timestamp');
        $this->assertGreaterThan(0, $track['creation_date_time'], 'creationDateTime should be a positive timestamp');
    }

    #[TestDox('Track distance is a positive number when present')]
    public function testTrackDistanceIsPositiveWhenPresent(): void
    {
        $this->client->request('GET', '/api/track?size=10');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $track) {
            if (isset($track['distance']) && $track['distance'] !== null) {
                $this->assertTrue(
                    is_float($track['distance']) || is_int($track['distance']),
                    'distance should be a float or int'
                );
                $this->assertGreaterThan(0, $track['distance']);
            }
        }
    }

    #[TestDox('Track points is a non-negative integer when present')]
    public function testTrackPointsIsNonNegativeWhenPresent(): void
    {
        $this->client->request('GET', '/api/track?size=10');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $track) {
            if (isset($track['points']) && $track['points'] !== null) {
                $this->assertIsInt($track['points']);
                $this->assertGreaterThanOrEqual(0, $track['points']);
            }
        }
    }

    #[TestDox('Track startDateTime and endDateTime are Unix timestamps when present')]
    public function testTrackDateTimesAreUnixTimestamps(): void
    {
        $this->client->request('GET', '/api/track?size=10');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $track) {
            if (isset($track['start_date_time']) && $track['start_date_time'] !== null) {
                $this->assertIsInt($track['start_date_time']);
                $this->assertGreaterThan(0, $track['start_date_time'], 'start_date_time should be a positive timestamp');
            }
            if (isset($track['end_date_time']) && $track['end_date_time'] !== null) {
                $this->assertIsInt($track['end_date_time']);
                $this->assertGreaterThan(0, $track['end_date_time'], 'end_date_time should be a positive timestamp');
            }
        }
    }

    #[TestDox('Track polyline is a string when present')]
    public function testTrackPolylineIsString(): void
    {
        $this->client->request('GET', '/api/track?size=10');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $track) {
            if (isset($track['polyline']) && $track['polyline'] !== null) {
                $this->assertIsString($track['polyline']);
            }
            if (isset($track['polylineString']) && $track['polylineString'] !== null) {
                $this->assertIsString($track['polylineString']);
            }
        }
    }

    #[TestDox('GET /api/track supports size parameter')]
    public function testTrackListSizeParameter(): void
    {
        $this->client->request('GET', '/api/track?size=3');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertLessThanOrEqual(3, count($response));
    }

    #[TestDox('GET /api/track/{nonExistentId} returns 404')]
    public function testTrackNotFoundReturns404(): void
    {
        $this->client->request('GET', '/api/track/999999');
        $this->assertResponseStatusCode(404);
    }
}
