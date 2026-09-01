<?php declare(strict_types=1);

namespace Tests\Mcp;

/**
 * Integrationstests der lesenden Tools gegen echte Entities.
 * Städte nutzen eindeutige Slugs, um Kollisionen mit CI-Fixtures zu vermeiden.
 */
final class ReadToolsTest extends AbstractMcpTestCase
{
    public function testGetCity(): void
    {
        $city = $this->createCity();
        $token = $this->obtainAccessToken('city:read');

        $result = $this->callTool($token, 'get_city', ['citySlug' => $city->getMainSlugString()]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertIsArray($result['json']);
    }

    public function testGetCityUnknownIsError(): void
    {
        $token = $this->obtainAccessToken('city:read');

        $result = $this->callTool($token, 'get_city', ['citySlug' => 'gibt-es-sicher-nicht-xyz']);

        self::assertTrue($result['isError']);
        self::assertStringContainsString('Unbekannte Stadt', $result['text']);
    }

    public function testListCitiesReturnsArray(): void
    {
        $this->createCity();
        $token = $this->obtainAccessToken('city:read');

        $result = $this->callTool($token, 'list_cities', []);

        self::assertFalse($result['isError'], $result['text']);
        self::assertIsArray($result['json']);
        self::assertNotEmpty($result['json']);
    }

    public function testGetRide(): void
    {
        $city = $this->createCity();
        $this->createRide($city, '2026-09-01 19:00:00');
        $token = $this->obtainAccessToken('ride:read');

        $result = $this->callTool($token, 'get_ride', ['citySlug' => $city->getMainSlugString(), 'rideIdentifier' => '2026-09-01']);

        self::assertFalse($result['isError'], $result['text']);
        self::assertIsArray($result['json']);
    }

    public function testGetRideUnknownIsError(): void
    {
        $token = $this->obtainAccessToken('ride:read');

        $result = $this->callTool($token, 'get_ride', ['citySlug' => 'gibt-es-nicht-xyz', 'rideIdentifier' => '2026-01-01']);

        self::assertTrue($result['isError']);
        self::assertStringContainsString('Kein Ride gefunden', $result['text']);
    }

    public function testListRidesReturnsArray(): void
    {
        $city = $this->createCity();
        $this->createRide($city, '2026-09-01 19:00:00');
        $token = $this->obtainAccessToken('ride:read');

        $result = $this->callTool($token, 'list_rides', ['citySlug' => $city->getMainSlugString()]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertIsArray($result['json']);
        self::assertNotEmpty($result['json']);
    }

    public function testGetCurrentRide(): void
    {
        $city = $this->createCity();
        $this->createRide($city, '2026-12-01 19:00:00');
        $token = $this->obtainAccessToken('ride:read');

        $result = $this->callTool($token, 'get_current_ride', ['citySlug' => $city->getMainSlugString()]);

        self::assertFalse($result['isError'], $result['text']);
    }

    public function testListPostsReturnsArray(): void
    {
        $token = $this->obtainAccessToken('post:read');

        $result = $this->callTool($token, 'list_posts', []);

        self::assertFalse($result['isError'], $result['text']);
        self::assertIsArray($result['json']);
    }

    public function testListTracksReturnsArray(): void
    {
        $token = $this->obtainAccessToken('track:read');

        $result = $this->callTool($token, 'list_tracks', []);

        self::assertFalse($result['isError'], $result['text']);
        self::assertIsArray($result['json']);
    }

    public function testListLocations(): void
    {
        $city = $this->createCity();
        $this->createLocation($city);
        $token = $this->obtainAccessToken('city:read');

        $result = $this->callTool($token, 'list_locations', ['citySlug' => $city->getMainSlugString()]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertCount(1, $result['json']);
    }

    public function testListCityCycles(): void
    {
        $city = $this->createCity();
        $this->createCityCycle($city);
        $token = $this->obtainAccessToken('city:read');

        $result = $this->callTool($token, 'list_city_cycles', ['citySlug' => $city->getMainSlugString()]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertCount(1, $result['json']);
    }

    public function testListSubrides(): void
    {
        $city = $this->createCity();
        $ride = $this->createRide($city, '2026-09-01 19:00:00');
        $this->createSubride($ride);
        $token = $this->obtainAccessToken('ride:read');

        $result = $this->callTool($token, 'list_subrides', ['citySlug' => $city->getMainSlugString(), 'rideIdentifier' => '2026-09-01']);

        self::assertFalse($result['isError'], $result['text']);
        self::assertCount(1, $result['json']);
    }

    public function testListRideTracks(): void
    {
        $city = $this->createCity();
        $ride = $this->createRide($city, '2026-09-01 19:00:00');
        $this->createTrack($ride);
        $token = $this->obtainAccessToken('track:read');

        $result = $this->callTool($token, 'list_ride_tracks', ['citySlug' => $city->getMainSlugString(), 'rideIdentifier' => '2026-09-01']);

        self::assertFalse($result['isError'], $result['text']);
        self::assertCount(1, $result['json']);
    }

    public function testGetTrack(): void
    {
        $city = $this->createCity();
        $ride = $this->createRide($city, '2026-09-01 19:00:00');
        $track = $this->createTrack($ride);
        $token = $this->obtainAccessToken('track:read');

        $result = $this->callTool($token, 'get_track', ['trackId' => $track->getId()]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertIsArray($result['json']);
    }

    public function testGetTrackUnknownIsError(): void
    {
        $token = $this->obtainAccessToken('track:read');

        $result = $this->callTool($token, 'get_track', ['trackId' => 999999]);

        self::assertTrue($result['isError']);
        self::assertStringContainsString('nicht gefunden', $result['text']);
    }

    public function testListRidePhotos(): void
    {
        $city = $this->createCity();
        $ride = $this->createRide($city, '2026-09-01 19:00:00');
        $this->createPhoto($ride, $city);
        $token = $this->obtainAccessToken('photo:read');

        $result = $this->callTool($token, 'list_ride_photos', ['citySlug' => $city->getMainSlugString(), 'rideIdentifier' => '2026-09-01']);

        self::assertFalse($result['isError'], $result['text']);
        self::assertCount(1, $result['json']);
    }

    public function testGetPhoto(): void
    {
        $city = $this->createCity();
        $ride = $this->createRide($city, '2026-09-01 19:00:00');
        $photo = $this->createPhoto($ride, $city);
        $token = $this->obtainAccessToken('photo:read');

        $result = $this->callTool($token, 'get_photo', ['photoId' => $photo->getId()]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertIsArray($result['json']);
    }
}
