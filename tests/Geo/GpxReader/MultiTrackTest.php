<?php declare(strict_types=1);

namespace Tests\Geo\GpxReader;

use App\Criticalmass\Geo\GpxReader\GpxReader;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;

class MultiTrackTest extends TestCase
{
    public function testWithTwoTrackSegments(): void
    {
        $gpxReader = new GpxReader($this->createFilesystemMock());
        $gpxReader->loadFromFile('test.gpx');

        $this->assertEquals(5, $gpxReader->countPoints());
    }

    protected function createFilesystemMock(): FilesystemInterface
    {
        $filesystem = $this->createMock(Filesystem::class);

        $filesystem->method('read')->willReturn('<?xml version="1.0" encoding="UTF-8"?>
<gpx creator="strava.com iPhone" version="1.1" xmlns="http://www.topografix.com/GPX/1/1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd" xmlns:gpxtpx="http://www.garmin.com/xmlschemas/TrackPointExtension/v1" xmlns:gpxx="http://www.garmin.com/xmlschemas/GpxExtensions/v3">
 <metadata>
  <time>2016-07-29T17:25:03Z</time>
 </metadata>
 <trk>
  <name>Critical Mass Hamburg</name>
  <trkseg>
   <trkpt lat="53.5645300" lon="9.9686650">
    <ele>15.2</ele>
    <time>2016-07-29T17:25:03Z</time>
    <extensions>
     <gpxtpx:TrackPointExtension>
      <gpxtpx:hr>71</gpxtpx:hr>
      <gpxtpx:cad>0</gpxtpx:cad>
     </gpxtpx:TrackPointExtension>
    </extensions>
   </trkpt>
   <trkpt lat="53.5645280" lon="9.9685810">
    <ele>15.3</ele>
    <time>2016-07-29T17:25:15Z</time>
    <extensions>
     <gpxtpx:TrackPointExtension>
      <gpxtpx:hr>71</gpxtpx:hr>
      <gpxtpx:cad>0</gpxtpx:cad>
     </gpxtpx:TrackPointExtension>
    </extensions>
   </trkpt>
   <trkpt lat="53.5645030" lon="9.9685640">
    <ele>15.4</ele>
    <time>2016-07-29T17:25:20Z</time>
    <extensions>
     <gpxtpx:TrackPointExtension>
      <gpxtpx:hr>71</gpxtpx:hr>
      <gpxtpx:cad>0</gpxtpx:cad>
     </gpxtpx:TrackPointExtension>
    </extensions>
   </trkpt>
  </trkseg>
  <trkseg>
   <trkpt lat="53.5645090" lon="9.9686100">
    <ele>15.3</ele>
    <time>2016-07-29T17:25:25Z</time>
    <extensions>
     <gpxtpx:TrackPointExtension>
      <gpxtpx:hr>69</gpxtpx:hr>
      <gpxtpx:cad>0</gpxtpx:cad>
     </gpxtpx:TrackPointExtension>
    </extensions>
   </trkpt>
   <trkpt lat="53.5644880" lon="9.9686390">
    <ele>15.3</ele>
    <time>2016-07-29T17:25:31Z</time>
    <extensions>
     <gpxtpx:TrackPointExtension>
      <gpxtpx:hr>74</gpxtpx:hr>
      <gpxtpx:cad>0</gpxtpx:cad>
     </gpxtpx:TrackPointExtension>
    </extensions>
   </trkpt>
  </trkseg>
 </trk>
</gpx>');

        return $filesystem;
    }
}
