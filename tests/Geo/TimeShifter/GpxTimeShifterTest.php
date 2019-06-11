<?php declare(strict_types=1);

namespace Tests\Geo\TimeShifter;

use App\Criticalmass\Geo\Converter\GpxToPositionListConverter;
use App\Criticalmass\Geo\GpxReader\GpxReader;
use App\Criticalmass\Geo\TimeShifter\GpxTimeShifter;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;

class GpxTimeShifterTest extends TestCase
{
    public function testOriginalDateTime()
    {
        $gpxReader = new GpxReader($this->createFilesystemMock());

        $dateTime = $gpxReader
            ->loadFromFile('test.gpx')
            ->getDateTimeOfPoint(5);

        $this->assertEquals(new \DateTime('2016-11-25 15:40:29', new \DateTimeZone('UTC')), $dateTime);
    }

    public function testGpxTimeShifterFiveMinutes()
    {
        $gpxReader = new GpxReader($this->createFilesystemMock());
        $gpxToPositionListConverter = new GpxToPositionListConverter($gpxReader);

        $timeShifter = new GpxTimeShifter($gpxToPositionListConverter);

        $interval = new \DateInterval('PT5M');

        $timeShifter
            ->loadGpxFile('test.gpx')
            ->shift($interval);

        $positionList = $timeShifter->getPositionList();

        $dateTime = $positionList
            ->get(5)
            ->getDateTime();

        $this->assertEquals(new \DateTime('2016-11-25 15:40:29', new \DateTimeZone('UTC')), $dateTime);
    }

    protected function createFilesystemMock()
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem
            ->method('read')
            ->willReturn('<?xml version="1.0" encoding="UTF-8"?>
<gpx creator="strava.com iPhone" version="1.1" xmlns="http://www.topografix.com/GPX/1/1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd" xmlns:gpxtpx="http://www.garmin.com/xmlschemas/TrackPointExtension/v1" xmlns:gpxx="http://www.garmin.com/xmlschemas/GpxExtensions/v3">
 <metadata>
  <time>2016-11-25T15:39:38Z</time>
 </metadata>
 <trk>
  <name>Zum Bahnhof</name>
  <trkseg>
   <trkpt lat="53.5487830" lon="9.9790980">
    <ele>23.3</ele>
    <time>2016-11-25T15:39:38Z</time>
   </trkpt>
   <trkpt lat="53.5493770" lon="9.9789250">
    <ele>25.5</ele>
    <time>2016-11-25T15:40:13Z</time>
   </trkpt>
   <trkpt lat="53.5493620" lon="9.9789640">
    <ele>25.3</ele>
    <time>2016-11-25T15:40:20Z</time>
   </trkpt>
   <trkpt lat="53.5493660" lon="9.9790330">
    <ele>25.0</ele>
    <time>2016-11-25T15:40:24Z</time>
   </trkpt>
   <trkpt lat="53.5493650" lon="9.9790790">
    <ele>24.8</ele>
    <time>2016-11-25T15:40:26Z</time>
   </trkpt>
   <trkpt lat="53.5493610" lon="9.9791320">
    <ele>24.6</ele>
    <time>2016-11-25T15:40:29Z</time>
   </trkpt>
  </trkseg>
 </trk>
</gpx>
');

        return $filesystem;
    }
}
