<?php declare(strict_types=1);

namespace Tests\Geo\Converter;

use App\Criticalmass\Geo\Converter\TrackToPositionListConverter;
use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\Entity\Track;
use App\Criticalmass\Geo\GpxReader\TrackReader;
use App\Criticalmass\Geo\PositionList\PositionList;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;

class TrackToPositionListConverterTest extends TestCase
{
    public function testLatLngTrackConverter(): void
    {
        $track = $this->createTestTrack();

        $trackReader = new TrackReader($this->createFilesystemMockForLatLngPosition());
        $trackReader->loadTrack($track);

        $converter = new TrackToPositionListConverter($trackReader);
        $actualPositionList = $converter->convert($track);

        $expectedPositionList = new PositionList();
        $expectedPositionList
            ->add(new Position(53.5487830, 9.9790980))
            ->add(new Position(53.5493770, 9.9789250))
            ->add(new Position(53.5493620, 9.9789640))
            ->add(new Position(53.5493660, 9.9790330))
            ->add(new Position(53.5493650, 9.9790790))
            ->add(new Position(53.5493610, 9.9791320));

        $this->assertEquals($expectedPositionList, $actualPositionList);
    }

    public function testLatLngDateTimeTrackConverter(): void
    {
        $track = $this->createTestTrack();

        $trackReader = new TrackReader($this->createFilesystemMockForLatLngDateTimePosition());
        $trackReader->loadTrack($track);

        $converter = new TrackToPositionListConverter($trackReader);
        $actualPositionList = $converter->convert($track);

        $expectedPositionList = new PositionList();
        $expectedPositionList
            ->add((new Position(53.5487830, 9.9790980))->setDateTime(new \DateTime('2016-11-25 15:39:38', new \DateTimeZone('UTC'))))
            ->add((new Position(53.5493770, 9.9789250))->setDateTime(new \DateTime('2016-11-25 15:40:13', new \DateTimeZone('UTC'))))
            ->add((new Position(53.5493620, 9.9789640))->setDateTime(new \DateTime('2016-11-25 15:40:20', new \DateTimeZone('UTC'))))
            ->add((new Position(53.5493660, 9.9790330))->setDateTime(new \DateTime('2016-11-25 15:40:24', new \DateTimeZone('UTC'))))
            ->add((new Position(53.5493650, 9.9790790))->setDateTime(new \DateTime('2016-11-25 15:40:26', new \DateTimeZone('UTC'))))
            ->add((new Position(53.5493610, 9.9791320))->setDateTime(new \DateTime('2016-11-25 15:40:29', new \DateTimeZone('UTC'))));

        $this->assertEquals($expectedPositionList, $actualPositionList);
    }

    public function testLatLngAltitudeTrackConverter(): void
    {
        $track = $this->createTestTrack();

        $trackReader = new TrackReader($this->createFilesystemMockForLatLngAltitudePosition());
        $trackReader->loadTrack($track);

        $converter = new TrackToPositionListConverter($trackReader);
        $actualPositionList = $converter->convert($track);

        $expectedPositionList = new PositionList();
        $expectedPositionList
            ->add((new Position(53.5487830, 9.9790980))->setAltitude(23.3))
            ->add((new Position(53.5493770, 9.9789250))->setAltitude(25.5))
            ->add((new Position(53.5493620, 9.9789640))->setAltitude(25.3))
            ->add((new Position(53.5493660, 9.9790330))->setAltitude(25.0))
            ->add((new Position(53.5493650, 9.9790790))->setAltitude(24.8))
            ->add((new Position(53.5493610, 9.9791320))->setAltitude(24.6));

        $this->assertEquals($expectedPositionList, $actualPositionList);
    }

    public function testLatLngDateTimeAltitudeTrackConverter(): void
    {
        $track = $this->createTestTrack();

        $trackReader = new TrackReader($this->createFilesystemMockForLatLngDateTimeAltitudePosition());
        $trackReader->loadTrack($track);

        $converter = new TrackToPositionListConverter($trackReader);
        $actualPositionList = $converter->convert($track);

        $expectedPositionList = new PositionList();
        $expectedPositionList
            ->add((new Position(53.5487830, 9.9790980))->setAltitude(23.3)->setDateTime(new \DateTime('2016-11-25 15:39:38', new \DateTimeZone('UTC'))))
            ->add((new Position(53.5493770, 9.9789250))->setAltitude(25.5)->setDateTime(new \DateTime('2016-11-25 15:40:13', new \DateTimeZone('UTC'))))
            ->add((new Position(53.5493620, 9.9789640))->setAltitude(25.3)->setDateTime(new \DateTime('2016-11-25 15:40:20', new \DateTimeZone('UTC'))))
            ->add((new Position(53.5493660, 9.9790330))->setAltitude(25.0)->setDateTime(new \DateTime('2016-11-25 15:40:24', new \DateTimeZone('UTC'))))
            ->add((new Position(53.5493650, 9.9790790))->setAltitude(24.8)->setDateTime(new \DateTime('2016-11-25 15:40:26', new \DateTimeZone('UTC'))))
            ->add((new Position(53.5493610, 9.9791320))->setAltitude(24.6)->setDateTime(new \DateTime('2016-11-25 15:40:29', new \DateTimeZone('UTC'))));

        $this->assertEquals($expectedPositionList, $actualPositionList);
    }

    public function testConverterWithStartEndPoints(): void
    {
        $track = $this->createTestTrack(7, 2, 4);

        $trackReader = new TrackReader($this->createFilesystemMockForLatLngPosition());
        $trackReader->loadTrack($track);

        $converter = new TrackToPositionListConverter($trackReader);
        $actualPositionList = $converter->convert($track);

        $expectedPositionList = new PositionList();
        $expectedPositionList
            ->add(new Position(53.5493620, 9.9789640))
            ->add(new Position(53.5493660, 9.9790330))
            ->add(new Position(53.5493650, 9.9790790));

        $this->assertEquals($expectedPositionList, $actualPositionList);
    }

    protected function createFilesystemMockForLatLngPosition(): FilesystemInterface
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
   <trkpt lat="53.5487830" lon="9.9790980" />
   <trkpt lat="53.5493770" lon="9.9789250" />
   <trkpt lat="53.5493620" lon="9.9789640" />
   <trkpt lat="53.5493660" lon="9.9790330" />
   <trkpt lat="53.5493650" lon="9.9790790" />
   <trkpt lat="53.5493610" lon="9.9791320" />
  </trkseg>
 </trk>
</gpx>
');

        return $filesystem;
    }

    protected function createFilesystemMockForLatLngDateTimePosition(): FilesystemInterface
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
    <time>2016-11-25T15:39:38Z</time>
   </trkpt>
   <trkpt lat="53.5493770" lon="9.9789250">
    <time>2016-11-25T15:40:13Z</time>
   </trkpt>
   <trkpt lat="53.5493620" lon="9.9789640">
    <time>2016-11-25T15:40:20Z</time>
   </trkpt>
   <trkpt lat="53.5493660" lon="9.9790330">
    <time>2016-11-25T15:40:24Z</time>
   </trkpt>
   <trkpt lat="53.5493650" lon="9.9790790">
    <time>2016-11-25T15:40:26Z</time>
   </trkpt>
   <trkpt lat="53.5493610" lon="9.9791320">
    <time>2016-11-25T15:40:29Z</time>
   </trkpt>
  </trkseg>
 </trk>
</gpx>
');

        return $filesystem;
    }

    protected function createFilesystemMockForLatLngAltitudePosition(): FilesystemInterface
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
   </trkpt>
   <trkpt lat="53.5493770" lon="9.9789250">
    <ele>25.5</ele>
   </trkpt>
   <trkpt lat="53.5493620" lon="9.9789640">
    <ele>25.3</ele>
   </trkpt>
   <trkpt lat="53.5493660" lon="9.9790330">
    <ele>25.0</ele>
   </trkpt>
   <trkpt lat="53.5493650" lon="9.9790790">
    <ele>24.8</ele>
   </trkpt>
   <trkpt lat="53.5493610" lon="9.9791320">
    <ele>24.6</ele>
   </trkpt>
  </trkseg>
 </trk>
</gpx>
');

        return $filesystem;
    }

    protected function createFilesystemMockForLatLngDateTimeAltitudePosition(): FilesystemInterface
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

    protected function createTestTrack(int $points = 6, int $startPoint = 0, int $endPoint = 5): Track
    {
        $track = new Track();
        $track
            ->setTrackFilename('test.gpx')
            ->setStartPoint($startPoint)
            ->setPoints($points)
            ->setEndPoint($endPoint);

        return $track;
    }
}
