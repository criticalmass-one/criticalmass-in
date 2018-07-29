<?php

namespace Caldera\GeoBundle\Test\DistanceCalculator;

use Caldera\GeoBundle\Entity\Position;
use Caldera\GeoBundle\GpxReader\GpxReader;
use PHPUnit\Framework\TestCase;

class GpxReaderTest extends TestCase
{
    public function testGpxReader1()
    {
        $gpxTestFilename = __DIR__.'/../Files/cmhh.gpx';

        $gpxReader = new GpxReader();
        $gpxReader
            ->loadFromFile($gpxTestFilename);

        $this->assertNotNull($gpxReader->getRootNode());
    }

    public function testCreationDateTime()
    {
        $gpxTestFilename = __DIR__.'/../Files/bahnhof.gpx';

        $gpxReader = new GpxReader();

        $creationDateTime = $gpxReader
            ->loadFromFile($gpxTestFilename)
            ->getCreationDateTime();

        $this->assertEquals(new \DateTime('2016-11-25 15:39:38'), $creationDateTime);
    }

    public function testStartDateTime()
    {
        $gpxTestFilename = __DIR__.'/../Files/bahnhof.gpx';

        $gpxReader = new GpxReader();

        $creationDateTime = $gpxReader
            ->loadFromFile($gpxTestFilename)
            ->getStartDateTime();

        $this->assertEquals(new \DateTime('2016-11-25 15:39:38'), $creationDateTime);
    }

    public function testEndDateTime()
    {
        $gpxTestFilename = __DIR__.'/../Files/bahnhof.gpx';

        $gpxReader = new GpxReader();

        $creationDateTime = $gpxReader
            ->loadFromFile($gpxTestFilename)
            ->getEndDateTime();

        $this->assertEquals(new \DateTime('2016-11-25 15:49:42'), $creationDateTime);
    }

    public function testCountPoints()
    {
        $gpxTestFilename = __DIR__.'/../Files/bahnhof.gpx';

        $gpxReader = new GpxReader();

        $countPoints = $gpxReader
            ->loadFromFile($gpxTestFilename)
            ->countPoints();

        $this->assertEquals(363, $countPoints);
    }

    public function testGetPositionLatitude()
    {
        $gpxTestFilename = __DIR__.'/../Files/bahnhof.gpx';

        $gpxReader = new GpxReader();

        $latitude = $gpxReader
            ->loadFromFile($gpxTestFilename)
            ->getLatitudeOfPoint(5);

        $this->assertEquals(53.549361, $latitude);
    }

    public function testGetPositionLongitude()
    {
        $gpxTestFilename = __DIR__.'/../Files/bahnhof.gpx';

        $gpxReader = new GpxReader();

        $longitude = $gpxReader
            ->loadFromFile($gpxTestFilename)
            ->getLongitudeOfPoint(5);

        $this->assertEquals(9.979132, $longitude);
    }

    public function testGetElevationOfPosition()
    {
        $gpxTestFilename = __DIR__.'/../Files/bahnhof.gpx';

        $gpxReader = new GpxReader();

        $elevation = $gpxReader
            ->loadFromFile($gpxTestFilename)
            ->getElevationOfPoint(5);

        $this->assertEquals(24.6, $elevation);
    }

    public function testGetDateTimeOfPosition()
    {
        $gpxTestFilename = __DIR__.'/../Files/bahnhof.gpx';

        $gpxReader = new GpxReader();

        $dateTime = $gpxReader
            ->loadFromFile($gpxTestFilename)
            ->getDateTimeOfPoint(5);

        $this->assertEquals(new \DateTime('2016-11-25 15:40:29'), $dateTime);
    }

    public function testGetPosition()
    {
        $gpxTestFilename = __DIR__.'/../Files/bahnhof.gpx';

        $gpxReader = new GpxReader();

        $position = $gpxReader
            ->loadFromFile($gpxTestFilename)
            ->getPosition(5);

        $expectedPosition = new Position(53.549361, 9.979132);
        $expectedPosition
            ->setAltitude(24.6)
            ->setDateTime(new \DateTime('2016-11-25 15:40:29'))
        ;

        $this->assertEquals($expectedPosition, $position);
    }
}
