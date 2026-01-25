<?php declare(strict_types=1);

namespace Tests\Strava\Stream;

use App\Criticalmass\Strava\Stream\StreamFactory;
use App\Criticalmass\Strava\Stream\StreamList;
use PHPUnit\Framework\TestCase;

class StreamFactoryTest extends TestCase
{
    public function testBuildReturnsStreamList(): void
    {
        $apiResponse = $this->createStravaApiResponse();

        $streamList = StreamFactory::build($apiResponse);

        $this->assertInstanceOf(StreamList::class, $streamList);
    }

    public function testBuildParsesLatLngStream(): void
    {
        $apiResponse = $this->createStravaApiResponse();

        $streamList = StreamFactory::build($apiResponse);
        $latLngStream = $streamList->getStream('latlng');

        $this->assertEquals('latlng', $latLngStream->getType());
        $this->assertEquals('distance', $latLngStream->getSeriesType());
        $this->assertEquals('high', $latLngStream->getResolution());
        $this->assertEquals(5, $latLngStream->getOriginalSize());

        $data = $latLngStream->getData();
        $this->assertCount(5, $data);
        $this->assertEquals([52.520008, 13.404954], $data[0]);
    }

    public function testBuildParsesAltitudeStream(): void
    {
        $apiResponse = $this->createStravaApiResponse();

        $streamList = StreamFactory::build($apiResponse);
        $altitudeStream = $streamList->getStream('altitude');

        $this->assertEquals('altitude', $altitudeStream->getType());
        $this->assertEquals('distance', $altitudeStream->getSeriesType());
        $this->assertEquals('high', $altitudeStream->getResolution());
        $this->assertEquals(5, $altitudeStream->getOriginalSize());

        $data = $altitudeStream->getData();
        $this->assertCount(5, $data);
        $this->assertEquals(100.0, $data[0]);
    }

    public function testBuildParsesTimeStream(): void
    {
        $apiResponse = $this->createStravaApiResponse();

        $streamList = StreamFactory::build($apiResponse);
        $timeStream = $streamList->getStream('time');

        $this->assertEquals('time', $timeStream->getType());
        $this->assertEquals('distance', $timeStream->getSeriesType());
        $this->assertEquals('high', $timeStream->getResolution());
        $this->assertEquals(5, $timeStream->getOriginalSize());

        $data = $timeStream->getData();
        $this->assertCount(5, $data);
        $this->assertEquals(0, $data[0]);
        $this->assertEquals(20, $data[4]);
    }

    public function testBuildSetsCorrectStreamListLength(): void
    {
        $apiResponse = $this->createStravaApiResponse();

        $streamList = StreamFactory::build($apiResponse);

        $this->assertEquals(5, $streamList->getLength());
    }

    public function testBuildWithDifferentStreamSizes(): void
    {
        $apiResponse = new \stdClass();

        $apiResponse->latlng = new \stdClass();
        $apiResponse->latlng->series_type = 'distance';
        $apiResponse->latlng->resolution = 'high';
        $apiResponse->latlng->original_size = 10;
        $apiResponse->latlng->data = array_fill(0, 10, [52.52, 13.40]);

        $apiResponse->altitude = new \stdClass();
        $apiResponse->altitude->series_type = 'distance';
        $apiResponse->altitude->resolution = 'high';
        $apiResponse->altitude->original_size = 5;
        $apiResponse->altitude->data = array_fill(0, 5, 100.0);

        $streamList = StreamFactory::build($apiResponse);

        // Length should be the maximum
        $this->assertEquals(10, $streamList->getLength());
    }

    public function testBuildWithSingleStream(): void
    {
        $apiResponse = new \stdClass();

        $apiResponse->time = new \stdClass();
        $apiResponse->time->series_type = 'distance';
        $apiResponse->time->resolution = 'high';
        $apiResponse->time->original_size = 3;
        $apiResponse->time->data = [0, 5, 10];

        $streamList = StreamFactory::build($apiResponse);

        $this->assertEquals(3, $streamList->getLength());
        $this->assertNotNull($streamList->getStream('time'));
    }

    public function testBuildWithEmptyData(): void
    {
        $apiResponse = new \stdClass();

        $apiResponse->latlng = new \stdClass();
        $apiResponse->latlng->series_type = 'distance';
        $apiResponse->latlng->resolution = 'high';
        $apiResponse->latlng->original_size = 0;
        $apiResponse->latlng->data = [];

        $streamList = StreamFactory::build($apiResponse);

        $this->assertEquals(0, $streamList->getLength());
        $this->assertEmpty($streamList->getStream('latlng')->getData());
    }

    public function testBuildWithLowResolution(): void
    {
        $apiResponse = new \stdClass();

        $apiResponse->latlng = new \stdClass();
        $apiResponse->latlng->series_type = 'distance';
        $apiResponse->latlng->resolution = 'low';
        $apiResponse->latlng->original_size = 100;
        $apiResponse->latlng->data = [[52.52, 13.40]];

        $streamList = StreamFactory::build($apiResponse);

        $this->assertEquals('low', $streamList->getStream('latlng')->getResolution());
    }

    public function testBuildPreservesAllStreamTypes(): void
    {
        $apiResponse = $this->createStravaApiResponse();

        $streamList = StreamFactory::build($apiResponse);
        $allStreams = $streamList->getStreamList();

        $this->assertArrayHasKey('latlng', $allStreams);
        $this->assertArrayHasKey('altitude', $allStreams);
        $this->assertArrayHasKey('time', $allStreams);
        $this->assertCount(3, $allStreams);
    }

    private function createStravaApiResponse(): \stdClass
    {
        $response = new \stdClass();

        // LatLng Stream
        $response->latlng = new \stdClass();
        $response->latlng->series_type = 'distance';
        $response->latlng->resolution = 'high';
        $response->latlng->original_size = 5;
        $response->latlng->data = [
            [52.520008, 13.404954],
            [52.521000, 13.405000],
            [52.522000, 13.406000],
            [52.523000, 13.407000],
            [52.524000, 13.408000],
        ];

        // Altitude Stream
        $response->altitude = new \stdClass();
        $response->altitude->series_type = 'distance';
        $response->altitude->resolution = 'high';
        $response->altitude->original_size = 5;
        $response->altitude->data = [100.0, 100.5, 101.0, 101.5, 102.0];

        // Time Stream
        $response->time = new \stdClass();
        $response->time->series_type = 'distance';
        $response->time->resolution = 'high';
        $response->time->original_size = 5;
        $response->time->data = [0, 5, 10, 15, 20];

        return $response;
    }
}
