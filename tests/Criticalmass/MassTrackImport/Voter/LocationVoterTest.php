<?php declare(strict_types=1);

namespace Tests\Criticalmass\MassTrackImport\Voter;

use App\Criticalmass\Geo\Coord\Coord;
use App\Criticalmass\MassTrackImport\Voter\LocationVoter;
use App\Entity\Ride;
use App\Entity\TrackImportCandidate;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LocationVoterTest extends TestCase
{
    private const RIDE_LATITUDE = 53.55;
    private const RIDE_LONGITUDE = 9.99;

    /**
     * GeoUtil approximates 1° latitude with 111.3 km, so the latitude offsets below map
     * to well-defined distances from the ride's meeting point.
     *
     * @return iterable<string, array{0: float, 1: float}>
     */
    public static function latitudeOffsets(): iterable
    {
        yield 'same spot' => [0.0, 1.0];
        yield '500 m away' => [0.0045, 1.0];
        yield '2 km away' => [0.018, 0.9];
        yield '10 km away' => [0.09, 0.8];
        yield '30 km away' => [0.27, 0.5];
        yield '60 km away' => [0.54, -1.0];
        yield 'other side of the country' => [3.0, -1.0];
    }

    #[Test]
    #[DataProvider('latitudeOffsets')]
    public function scoreDropsWithDistanceFromMeetingPoint(float $latitudeOffset, float $expected): void
    {
        $ride = new Ride();
        $ride->setLatitude(self::RIDE_LATITUDE);
        $ride->setLongitude(self::RIDE_LONGITUDE);
        $candidate = (new TrackImportCandidate())->setStartCoord(new Coord(self::RIDE_LATITUDE + $latitudeOffset, self::RIDE_LONGITUDE));

        self::assertSame($expected, (new LocationVoter())->vote($ride, $candidate));
    }

    #[Test]
    public function rideWithoutCoordinatesIsVetoed(): void
    {
        $candidate = (new TrackImportCandidate())->setStartCoord(new Coord(self::RIDE_LATITUDE, self::RIDE_LONGITUDE));

        $latitudeOnly = new Ride();
        $latitudeOnly->setLatitude(self::RIDE_LATITUDE);

        self::assertSame(-1.0, (new LocationVoter())->vote(new Ride(), $candidate));
        self::assertSame(-1.0, (new LocationVoter())->vote($latitudeOnly, $candidate));
    }
}
