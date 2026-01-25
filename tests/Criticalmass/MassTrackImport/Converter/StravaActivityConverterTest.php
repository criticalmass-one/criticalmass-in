<?php declare(strict_types=1);

namespace Tests\Criticalmass\MassTrackImport\Converter;

use App\Criticalmass\MassTrackImport\Converter\StravaActivityConverter;
use App\Entity\TrackImportCandidate;
use PHPUnit\Framework\TestCase;

class StravaActivityConverterTest extends TestCase
{
    public function testConvertBasicActivity(): void
    {
        $activityData = [
            'id' => 123456789,
            'name' => 'Critical Mass Hamburg',
            'distance' => 25000.0,
            'elapsed_time' => 7200,
            'start_date' => '2024-01-15T18:00:00Z',
            'start_latlng' => [53.5511, 9.9937],
            'end_latlng' => [53.5611, 10.0037],
            'type' => 'Ride',
            'map' => [
                'summary_polyline' => 'encodedPolylineString'
            ]
        ];

        $candidate = StravaActivityConverter::convert($activityData);

        $this->assertInstanceOf(TrackImportCandidate::class, $candidate);
        $this->assertSame(123456789, $candidate->getActivityId());
        $this->assertSame('Critical Mass Hamburg', $candidate->getName());
        $this->assertSame(25000.0, $candidate->getDistance());
        $this->assertSame(7200, $candidate->getElapsedTime());
        $this->assertSame('Ride', $candidate->getType());
        $this->assertSame('encodedPolylineString', $candidate->getPolyline());
    }

    public function testConvertActivityCoordinates(): void
    {
        $activityData = [
            'id' => 1,
            'name' => 'Test',
            'distance' => 1000.0,
            'elapsed_time' => 600,
            'start_date' => '2024-01-15T18:00:00Z',
            'start_latlng' => [52.5200, 13.4050],  // Berlin
            'end_latlng' => [52.5300, 13.4150],
            'type' => 'Ride',
            'map' => [
                'summary_polyline' => 'abc'
            ]
        ];

        $candidate = StravaActivityConverter::convert($activityData);

        $this->assertSame(52.5200, $candidate->getStartLatitude());
        $this->assertSame(13.4050, $candidate->getStartLongitude());
        $this->assertSame(52.5300, $candidate->getEndLatitude());
        $this->assertSame(13.4150, $candidate->getEndLongitude());
    }

    public function testConvertActivityStartDateTime(): void
    {
        $activityData = [
            'id' => 1,
            'name' => 'Test',
            'distance' => 1000.0,
            'elapsed_time' => 600,
            'start_date' => '2024-06-28T18:30:00Z',
            'start_latlng' => [50.0, 10.0],
            'end_latlng' => [50.1, 10.1],
            'type' => 'Ride',
            'map' => [
                'summary_polyline' => 'abc'
            ]
        ];

        $candidate = StravaActivityConverter::convert($activityData);

        $this->assertInstanceOf(\DateTime::class, $candidate->getStartDateTime());
        $this->assertSame('2024-06-28', $candidate->getStartDateTime()->format('Y-m-d'));
    }

    public function testConvertActivityWithNegativeCoordinates(): void
    {
        $activityData = [
            'id' => 1,
            'name' => 'Sydney Ride',
            'distance' => 5000.0,
            'elapsed_time' => 1200,
            'start_date' => '2024-01-15T10:00:00Z',
            'start_latlng' => [-33.8688, 151.2093],  // Sydney
            'end_latlng' => [-33.8788, 151.2193],
            'type' => 'Ride',
            'map' => [
                'summary_polyline' => 'xyz'
            ]
        ];

        $candidate = StravaActivityConverter::convert($activityData);

        $this->assertSame(-33.8688, $candidate->getStartLatitude());
        $this->assertSame(151.2093, $candidate->getStartLongitude());
        $this->assertSame(-33.8788, $candidate->getEndLatitude());
        $this->assertSame(151.2193, $candidate->getEndLongitude());
    }

    /**
     * @dataProvider activityTypeProvider
     */
    public function testConvertDifferentActivityTypes(string $type): void
    {
        $activityData = [
            'id' => 1,
            'name' => 'Test Activity',
            'distance' => 1000.0,
            'elapsed_time' => 600,
            'start_date' => '2024-01-15T10:00:00Z',
            'start_latlng' => [50.0, 10.0],
            'end_latlng' => [50.1, 10.1],
            'type' => $type,
            'map' => [
                'summary_polyline' => 'abc'
            ]
        ];

        $candidate = StravaActivityConverter::convert($activityData);

        $this->assertSame($type, $candidate->getType());
    }

    public static function activityTypeProvider(): array
    {
        return [
            'Ride' => ['Ride'],
            'Run' => ['Run'],
            'Walk' => ['Walk'],
            'Hike' => ['Hike'],
            'VirtualRide' => ['VirtualRide'],
            'Swim' => ['Swim'],
        ];
    }

    public function testConvertActivityWithLongPolyline(): void
    {
        $longPolyline = str_repeat('abcdefghij', 1000);  // 10000 characters

        $activityData = [
            'id' => 1,
            'name' => 'Long Ride',
            'distance' => 100000.0,
            'elapsed_time' => 36000,
            'start_date' => '2024-01-15T06:00:00Z',
            'start_latlng' => [50.0, 10.0],
            'end_latlng' => [51.0, 11.0],
            'type' => 'Ride',
            'map' => [
                'summary_polyline' => $longPolyline
            ]
        ];

        $candidate = StravaActivityConverter::convert($activityData);

        $this->assertSame($longPolyline, $candidate->getPolyline());
        $this->assertSame(10000, strlen($candidate->getPolyline()));
    }

    public function testConvertActivityWithSpecialCharactersInName(): void
    {
        $activityData = [
            'id' => 1,
            'name' => 'Critical Mass München 🚲',
            'distance' => 1000.0,
            'elapsed_time' => 600,
            'start_date' => '2024-01-15T18:00:00Z',
            'start_latlng' => [48.1351, 11.5820],
            'end_latlng' => [48.1451, 11.5920],
            'type' => 'Ride',
            'map' => [
                'summary_polyline' => 'abc'
            ]
        ];

        $candidate = StravaActivityConverter::convert($activityData);

        $this->assertSame('Critical Mass München 🚲', $candidate->getName());
    }

    public function testConvertActivityWithZeroDistance(): void
    {
        $activityData = [
            'id' => 1,
            'name' => 'Stationary Activity',
            'distance' => 0.0,
            'elapsed_time' => 3600,
            'start_date' => '2024-01-15T10:00:00Z',
            'start_latlng' => [50.0, 10.0],
            'end_latlng' => [50.0, 10.0],
            'type' => 'Ride',
            'map' => [
                'summary_polyline' => ''
            ]
        ];

        $candidate = StravaActivityConverter::convert($activityData);

        $this->assertSame(0.0, $candidate->getDistance());
        $this->assertSame('', $candidate->getPolyline());
    }
}
