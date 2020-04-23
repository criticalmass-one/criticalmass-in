<?php declare(strict_types=1);

namespace Tests\Geo\GpxWriter;

use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\GpxWriter\GpxWriter;
use App\Criticalmass\Geo\PositionList\PositionList;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;

class GpxSavingTest extends TestCase
{
    public function testGpxWriterSave(): void
    {
        $expectedContent = '<?xml version="1.0"?>
<gpx xmlns="http://www.topografix.com/GPX/1/1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd">
 <trk>
  <trkseg>
   <trkpt lat="53.550556" lon="9.993333"/>
  </trkseg>
 </trk>
</gpx>
';
        $positionList = new PositionList();
        $positionList
            ->add(new Position(53.550556, 9.993333));

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->once())
            ->method('put')
            ->with(
                $this->equalTo('test.gpx'),
                $this->equalTo($expectedContent));

        $gpxWriter = new GpxWriter($filesystem);

        $gpxWriter
            ->setPositionList($positionList)
            ->generateGpxContent()
            ->saveGpxContent('test.gpx');
    }

    public function testGpxWriterDoesNotSaveEmptyFiles(): void
    {
        $positionList = new PositionList();
        $positionList
            ->add(new Position(53.550556, 9.993333));

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->never())
            ->method('put')
            ->with(
                $this->equalTo('test.gpx'),
                $this->equalTo(''));

        $gpxWriter = new GpxWriter($filesystem);

        $gpxWriter
            ->setPositionList($positionList)
            ->saveGpxContent('test.gpx');
    }
}
