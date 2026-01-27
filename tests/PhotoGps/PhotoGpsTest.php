<?php declare(strict_types=1);

namespace Tests\PhotoGps;

use App\Criticalmass\Geo\GpxService\GpxServiceInterface;
use App\Criticalmass\Image\ExifWrapper\ExifWrapper;
use App\Criticalmass\Image\PhotoGps\PhotoGps;
use App\Entity\Photo;
use App\Entity\Track;
use phpGPX\Models\Point;
use PHPExif\Exif;
use PHPUnit\Framework\TestCase;

class PhotoGpsTest extends TestCase
{
    public function testPhotoWithoutCoords(): void
    {
        $exifWrapper = $this->createMock(ExifWrapper::class);
        $gpxService = $this->createMock(GpxServiceInterface::class);

        $photo = new Photo();

        $photoGps = new PhotoGps($gpxService, $exifWrapper);

        $photoGps->setPhoto($photo)->execute();

        $this->assertFalse($photo->hasCoordinates());
        $this->assertNull($photo->getLatitude());
        $this->assertNull($photo->getLongitude());
    }

    public function testPhotoWithCoords(): void
    {
        $gpxService = $this->createMock(GpxServiceInterface::class);
        $exifWrapper = $this->createMock(ExifWrapper::class);
        $exif = $this->createMock(Exif::class);

        $exif->method('getGPS')->willReturn('52.266666666667,10.5');
        $exifWrapper->method('getExifData')->willReturn($exif);

        $photoGps = new PhotoGps($gpxService, $exifWrapper);

        $photo = new Photo();

        $photoGps->setPhoto($photo)->execute();

        $this->assertTrue($photo->hasCoordinates());
        $this->assertEquals(52.266666666667, $photo->getLatitude());
        $this->assertEquals(10.5, $photo->getLongitude());
    }

    public function testPhotoTrackCoords(): void
    {
        $exif = $this->createMock(Exif::class);
        $exif->method('getCreationDate')->willReturn(new \Carbon\Carbon('2019-06-24 19:25:00'));

        $exifWrapper = $this->createMock(ExifWrapper::class);
        $exifWrapper->method('getExifData')->willReturn($exif);

        $point = new Point(Point::TRACKPOINT);
        $point->latitude = 52.268021;
        $point->longitude = 10.500126;

        $gpxService = $this->createMock(GpxServiceInterface::class);
        $gpxService->method('findPointAtTime')->willReturn($point);

        $photoGps = new PhotoGps($gpxService, $exifWrapper);

        $track = $this->createMock(Track::class);

        $photo = new Photo();

        $photoGps
            ->setPhoto($photo)
            ->setDateTimeZone(new \DateTimeZone('Europe/Berlin'))
            ->setTrack($track)
            ->execute();

        $this->assertTrue($photo->hasCoordinates());
        $this->assertEquals(52.268021, $photo->getLatitude());
        $this->assertEquals(10.500126, $photo->getLongitude());
    }
}
