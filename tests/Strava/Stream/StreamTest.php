<?php declare(strict_types=1);

namespace Tests\Strava\Stream;

use App\Criticalmass\Strava\Stream\Stream;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    public function testSetAndGetType(): void
    {
        $stream = new Stream();
        $stream->setType('latlng');

        $this->assertEquals('latlng', $stream->getType());
    }

    public function testSetAndGetSeriesType(): void
    {
        $stream = new Stream();
        $stream->setSeriesType('distance');

        $this->assertEquals('distance', $stream->getSeriesType());
    }

    public function testSetAndGetOriginalSize(): void
    {
        $stream = new Stream();
        $stream->setOriginalSize(1500);

        $this->assertEquals(1500, $stream->getOriginalSize());
    }

    public function testSetAndGetResolution(): void
    {
        $stream = new Stream();
        $stream->setResolution('high');

        $this->assertEquals('high', $stream->getResolution());
    }

    public function testSetAndGetData(): void
    {
        $stream = new Stream();
        $data = [
            [52.520008, 13.404954],
            [52.521000, 13.405000],
            [52.522000, 13.406000],
        ];
        $stream->setData($data);

        $this->assertEquals($data, $stream->getData());
    }

    public function testFluentInterface(): void
    {
        $stream = new Stream();
        $result = $stream
            ->setType('altitude')
            ->setSeriesType('distance')
            ->setOriginalSize(100)
            ->setResolution('high')
            ->setData([100.5, 101.2, 102.8]);

        $this->assertInstanceOf(Stream::class, $result);
        $this->assertEquals('altitude', $stream->getType());
        $this->assertEquals('distance', $stream->getSeriesType());
        $this->assertEquals(100, $stream->getOriginalSize());
        $this->assertEquals('high', $stream->getResolution());
        $this->assertEquals([100.5, 101.2, 102.8], $stream->getData());
    }

    public function testLatLngStreamData(): void
    {
        $stream = new Stream();
        $latLngData = [
            [52.520008, 13.404954],
            [52.521000, 13.405000],
            [52.522000, 13.406000],
            [52.523000, 13.407000],
        ];

        $stream
            ->setType('latlng')
            ->setSeriesType('distance')
            ->setOriginalSize(4)
            ->setResolution('high')
            ->setData($latLngData);

        $this->assertCount(4, $stream->getData());
        $this->assertEquals([52.520008, 13.404954], $stream->getData()[0]);
    }

    public function testAltitudeStreamData(): void
    {
        $stream = new Stream();
        $altitudeData = [100.5, 101.2, 102.8, 103.5];

        $stream
            ->setType('altitude')
            ->setSeriesType('distance')
            ->setOriginalSize(4)
            ->setResolution('high')
            ->setData($altitudeData);

        $this->assertCount(4, $stream->getData());
        $this->assertEquals(100.5, $stream->getData()[0]);
    }

    public function testTimeStreamData(): void
    {
        $stream = new Stream();
        $timeData = [0, 5, 10, 15, 20];

        $stream
            ->setType('time')
            ->setSeriesType('distance')
            ->setOriginalSize(5)
            ->setResolution('high')
            ->setData($timeData);

        $this->assertCount(5, $stream->getData());
        $this->assertEquals(0, $stream->getData()[0]);
        $this->assertEquals(20, $stream->getData()[4]);
    }

    public function testEmptyData(): void
    {
        $stream = new Stream();
        $stream->setData([]);

        $this->assertEmpty($stream->getData());
    }
}
