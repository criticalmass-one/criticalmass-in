<?php declare(strict_types=1);

namespace Tests\Strava\Stream;

use App\Criticalmass\Strava\Stream\Stream;
use App\Criticalmass\Strava\Stream\StreamList;
use PHPUnit\Framework\TestCase;

class StreamListTest extends TestCase
{
    public function testAddAndGetStream(): void
    {
        $streamList = new StreamList();

        $stream = new Stream();
        $stream
            ->setType('latlng')
            ->setOriginalSize(100)
            ->setData([[52.52, 13.40]]);

        $streamList->addStream('latlng', $stream);

        $this->assertSame($stream, $streamList->getStream('latlng'));
    }

    public function testSetAndGetStreamList(): void
    {
        $streamList = new StreamList();

        $latLngStream = new Stream();
        $latLngStream->setType('latlng')->setOriginalSize(50);

        $altitudeStream = new Stream();
        $altitudeStream->setType('altitude')->setOriginalSize(50);

        $streams = [
            'latlng' => $latLngStream,
            'altitude' => $altitudeStream,
        ];

        $streamList->setStreamList($streams);

        $this->assertEquals($streams, $streamList->getStreamList());
    }

    public function testGetLengthWithSingleStream(): void
    {
        $streamList = new StreamList();

        $stream = new Stream();
        $stream->setOriginalSize(150);

        $streamList->addStream('latlng', $stream);

        $this->assertEquals(150, $streamList->getLength());
    }

    public function testGetLengthWithMultipleStreams(): void
    {
        $streamList = new StreamList();

        $stream1 = new Stream();
        $stream1->setOriginalSize(100);

        $stream2 = new Stream();
        $stream2->setOriginalSize(200);

        $stream3 = new Stream();
        $stream3->setOriginalSize(150);

        $streamList->addStream('latlng', $stream1);
        $streamList->addStream('altitude', $stream2);
        $streamList->addStream('time', $stream3);

        // Length should be the maximum of all streams
        $this->assertEquals(200, $streamList->getLength());
    }

    public function testFluentInterface(): void
    {
        $streamList = new StreamList();

        $stream = new Stream();
        $stream->setOriginalSize(10);

        $result = $streamList->addStream('test', $stream);

        $this->assertInstanceOf(StreamList::class, $result);
    }

    public function testCompleteStravaStreamStructure(): void
    {
        $streamList = new StreamList();

        // LatLng Stream
        $latLngStream = new Stream();
        $latLngStream
            ->setType('latlng')
            ->setSeriesType('distance')
            ->setOriginalSize(5)
            ->setResolution('high')
            ->setData([
                [52.520008, 13.404954],
                [52.521000, 13.405000],
                [52.522000, 13.406000],
                [52.523000, 13.407000],
                [52.524000, 13.408000],
            ]);

        // Altitude Stream
        $altitudeStream = new Stream();
        $altitudeStream
            ->setType('altitude')
            ->setSeriesType('distance')
            ->setOriginalSize(5)
            ->setResolution('high')
            ->setData([100.0, 100.5, 101.0, 101.5, 102.0]);

        // Time Stream
        $timeStream = new Stream();
        $timeStream
            ->setType('time')
            ->setSeriesType('distance')
            ->setOriginalSize(5)
            ->setResolution('high')
            ->setData([0, 5, 10, 15, 20]);

        $streamList
            ->addStream('latlng', $latLngStream)
            ->addStream('altitude', $altitudeStream)
            ->addStream('time', $timeStream);

        $this->assertEquals(5, $streamList->getLength());
        $this->assertCount(5, $streamList->getStream('latlng')->getData());
        $this->assertCount(5, $streamList->getStream('altitude')->getData());
        $this->assertCount(5, $streamList->getStream('time')->getData());
    }

    public function testInitialLengthIsZero(): void
    {
        $streamList = new StreamList();

        $this->assertEquals(0, $streamList->getLength());
    }

    public function testAddStreamUpdatesLength(): void
    {
        $streamList = new StreamList();
        $this->assertEquals(0, $streamList->getLength());

        $stream = new Stream();
        $stream->setOriginalSize(42);

        $streamList->addStream('test', $stream);
        $this->assertEquals(42, $streamList->getLength());
    }

    public function testSmallerStreamDoesNotReduceLength(): void
    {
        $streamList = new StreamList();

        $largeStream = new Stream();
        $largeStream->setOriginalSize(100);

        $smallStream = new Stream();
        $smallStream->setOriginalSize(50);

        $streamList->addStream('large', $largeStream);
        $streamList->addStream('small', $smallStream);

        // Length should remain 100, not be reduced to 50
        $this->assertEquals(100, $streamList->getLength());
    }
}
