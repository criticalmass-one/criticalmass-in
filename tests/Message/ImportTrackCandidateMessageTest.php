<?php declare(strict_types=1);

namespace Tests\Message;

use App\Message\ImportTrackCandidateMessage;
use PHPUnit\Framework\TestCase;

class ImportTrackCandidateMessageTest extends TestCase
{
    public function testCreateMessage(): void
    {
        $startDateTime = new \Carbon\Carbon('2024-01-15 18:00:00');

        $message = new ImportTrackCandidateMessage(
            userId: 42,
            activityId: 123456789,
            name: 'Critical Mass Hamburg',
            distance: 25.5,
            elapsedTime: 7200,
            type: 'Ride',
            startDateTime: $startDateTime,
            startLatitude: 53.5511,
            startLongitude: 9.9937,
            endLatitude: 53.5611,
            endLongitude: 10.0037,
            polyline: 'encodedPolylineString'
        );

        $this->assertSame(42, $message->getUserId());
        $this->assertSame(123456789, $message->getActivityId());
        $this->assertSame('Critical Mass Hamburg', $message->getName());
        $this->assertSame(25.5, $message->getDistance());
        $this->assertSame(7200, $message->getElapsedTime());
        $this->assertSame('Ride', $message->getType());
        $this->assertSame($startDateTime, $message->getStartDateTime());
        $this->assertSame(53.5511, $message->getStartLatitude());
        $this->assertSame(9.9937, $message->getStartLongitude());
        $this->assertSame(53.5611, $message->getEndLatitude());
        $this->assertSame(10.0037, $message->getEndLongitude());
        $this->assertSame('encodedPolylineString', $message->getPolyline());
    }

    public function testMessageWithZeroValues(): void
    {
        $message = new ImportTrackCandidateMessage(
            userId: 1,
            activityId: 1,
            name: '',
            distance: 0.0,
            elapsedTime: 0,
            type: 'Ride',
            startDateTime: new \Carbon\Carbon(),
            startLatitude: 0.0,
            startLongitude: 0.0,
            endLatitude: 0.0,
            endLongitude: 0.0,
            polyline: ''
        );

        $this->assertSame(0.0, $message->getDistance());
        $this->assertSame(0, $message->getElapsedTime());
        $this->assertSame('', $message->getName());
        $this->assertSame('', $message->getPolyline());
    }

    public function testMessageWithNegativeCoordinates(): void
    {
        $message = new ImportTrackCandidateMessage(
            userId: 1,
            activityId: 1,
            name: 'Test Ride',
            distance: 10.0,
            elapsedTime: 3600,
            type: 'Ride',
            startDateTime: new \Carbon\Carbon(),
            startLatitude: -33.8688,  // Sydney
            startLongitude: 151.2093,
            endLatitude: -33.8788,
            endLongitude: 151.2193,
            polyline: 'test'
        );

        $this->assertSame(-33.8688, $message->getStartLatitude());
        $this->assertSame(151.2093, $message->getStartLongitude());
        $this->assertSame(-33.8788, $message->getEndLatitude());
        $this->assertSame(151.2193, $message->getEndLongitude());
    }

    public function testMessageIsImmutable(): void
    {
        $startDateTime = new \Carbon\Carbon('2024-01-15 18:00:00');

        $message = new ImportTrackCandidateMessage(
            userId: 42,
            activityId: 123,
            name: 'Test',
            distance: 10.0,
            elapsedTime: 3600,
            type: 'Ride',
            startDateTime: $startDateTime,
            startLatitude: 50.0,
            startLongitude: 10.0,
            endLatitude: 51.0,
            endLongitude: 11.0,
            polyline: 'abc'
        );

        // Verify all getters return the same values
        $this->assertSame(42, $message->getUserId());
        $this->assertSame(123, $message->getActivityId());
        $this->assertSame('Test', $message->getName());
    }

    /**
     * @dataProvider activityTypeProvider
     */
    public function testDifferentActivityTypes(string $type): void
    {
        $message = new ImportTrackCandidateMessage(
            userId: 1,
            activityId: 1,
            name: 'Test',
            distance: 10.0,
            elapsedTime: 3600,
            type: $type,
            startDateTime: new \Carbon\Carbon(),
            startLatitude: 50.0,
            startLongitude: 10.0,
            endLatitude: 51.0,
            endLongitude: 11.0,
            polyline: 'abc'
        );

        $this->assertSame($type, $message->getType());
    }

    public static function activityTypeProvider(): array
    {
        return [
            'Ride' => ['Ride'],
            'Run' => ['Run'],
            'Walk' => ['Walk'],
            'Hike' => ['Hike'],
            'VirtualRide' => ['VirtualRide'],
        ];
    }
}
