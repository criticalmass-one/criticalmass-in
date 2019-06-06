<?php declare(strict_types=1);

namespace Tests\Geo\GpxWriter;

use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\GpxWriter\GpxWriter;
use App\Criticalmass\Geo\PositionList\PositionList;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;

class GpxGenerationTest extends TestCase
{
    public function testGpxWriterReturnsEmptyStringBeforeGeneration(): void
    {
        $filesystem = $this->createMock(Filesystem::class);
        $gpxWriter = new GpxWriter($filesystem);

        $actualContent = $gpxWriter->getGpxContent();

        $expectedContent = '';

        $this->assertEquals($expectedContent, $actualContent);
    }

    public function testGpxWriterWithoutPositions(): void
    {
        $filesystem = $this->createMock(Filesystem::class);
        $gpxWriter = new GpxWriter($filesystem);

        $actualContent = $gpxWriter->generateGpxContent()->getGpxContent();

        $expectedContent = "<?xml version=\"1.0\"?>
<gpx xmlns=\"http://www.topografix.com/GPX/1/1\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd\">
 <trk>
  <trkseg/>
 </trk>
</gpx>
";

        $this->assertEquals($expectedContent, $actualContent);
    }

    public function testGpxWriterWithSmallPositionList(): void
    {
        $positionList = new PositionList();
        $positionList
            ->add(new Position(53.550556, 9.993333))
            ->add(new Position(52.518611, 13.408333));

        $filesystem = $this->createMock(Filesystem::class);
        $gpxWriter = new GpxWriter($filesystem);

        $actualContent = $gpxWriter
            ->setPositionList($positionList)
            ->generateGpxContent()
            ->getGpxContent();

        $expectedContent = "<?xml version=\"1.0\"?>
<gpx xmlns=\"http://www.topografix.com/GPX/1/1\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd\">
 <trk>
  <trkseg>
   <trkpt lat=\"53.550556\" lon=\"9.993333\"/>
   <trkpt lat=\"52.518611\" lon=\"13.408333\"/>
  </trkseg>
 </trk>
</gpx>
";

        $this->assertEquals($expectedContent, $actualContent);
    }

    public function testGpxWriterWithSmallPositionListAndDateTime(): void
    {
        $positionList = new PositionList();
        $positionList
            ->add((new Position(53.550556, 9.993333))->setDateTime(new \DateTime('2011-06-24 19:00:00')))
            ->add((new Position(52.518611, 13.408333))->setDateTime(new \DateTime('2011-06-24 19:10:00')));

        $filesystem = $this->createMock(Filesystem::class);
        $gpxWriter = new GpxWriter($filesystem);

        $actualContent = $gpxWriter
            ->setPositionList($positionList)
            ->generateGpxContent()
            ->getGpxContent();

        $expectedContent = "<?xml version=\"1.0\"?>
<gpx xmlns=\"http://www.topografix.com/GPX/1/1\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd\">
 <metadata>
  <time>2011-06-24T19:00:00Z</time>
 </metadata>
 <trk>
  <trkseg>
   <trkpt lat=\"53.550556\" lon=\"9.993333\">
    <time>2011-06-24T19:00:00Z</time>
   </trkpt>
   <trkpt lat=\"52.518611\" lon=\"13.408333\">
    <time>2011-06-24T19:10:00Z</time>
   </trkpt>
  </trkseg>
 </trk>
</gpx>
";

        $this->assertEquals($expectedContent, $actualContent);
    }

    public function testGpxWriterWithSmallPositionListAndAltitude(): void
    {
        $positionList = new PositionList();
        $positionList
            ->add((new Position(53.550556, 9.993333))->setAltitude(5))
            ->add((new Position(52.518611, 13.408333))->setAltitude(10));

        $filesystem = $this->createMock(Filesystem::class);
        $gpxWriter = new GpxWriter($filesystem);

        $actualContent = $gpxWriter
            ->setPositionList($positionList)
            ->generateGpxContent()
            ->getGpxContent();

        $expectedContent = "<?xml version=\"1.0\"?>
<gpx xmlns=\"http://www.topografix.com/GPX/1/1\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd\">
 <trk>
  <trkseg>
   <trkpt lat=\"53.550556\" lon=\"9.993333\">
    <ele>5</ele>
   </trkpt>
   <trkpt lat=\"52.518611\" lon=\"13.408333\">
    <ele>10</ele>
   </trkpt>
  </trkseg>
 </trk>
</gpx>
";

        $this->assertEquals($expectedContent, $actualContent);
    }
}
