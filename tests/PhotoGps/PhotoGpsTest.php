<?php declare(strict_types=1);

namespace Tests\PhotoGps;

use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\GpxReader\TrackReader;
use App\Criticalmass\Geo\Loop\Loop;
use App\Criticalmass\Image\ExifWrapper\ExifWrapper;
use App\Criticalmass\Image\PhotoGps\PhotoGps;
use App\Entity\Photo;
use App\Entity\Track;
use PHPExif\Exif;
use PHPUnit\Framework\TestCase;

class PhotoGpsTest extends TestCase
{
    public function testPhotoWithoutCoords(): void
    {
        $exifWrapper = $this->createMock(ExifWrapper::class);
        $trackReader = $this->createMock(TrackReader::class);
        $loop = $this->createMock(Loop::class);

        $photo = new Photo();

        $photoGps = new PhotoGps($trackReader, $exifWrapper, $loop);

        $photoGps->setPhoto($photo)->execute();

        $this->assertFalse($photo->hasCoordinates());
        $this->assertNull($photo->getLatitude());
        $this->assertNull($photo->getLongitude());
    }

    public function testPhotoWithCoords(): void
    {
        $trackReader = $this->createMock(TrackReader::class);
        $exifWrapper = $this->createMock(ExifWrapper::class);
        $exif = $this->createMock(Exif::class);

        $loop = $this->createMock(Loop::class);

        $exif->method('getGPS')->willReturn('52.266666666667,10.5');
        $exifWrapper->method('getExifData')->willReturn($exif);

        $photoGps = new PhotoGps($trackReader, $exifWrapper, $loop);

        $photo = new Photo();

        $photoGps->setPhoto($photo)->execute();

        $this->assertTrue($photo->hasCoordinates());
        $this->assertEquals(52.266666666667, $photo->getLatitude());
        $this->assertEquals(10.5, $photo->getLongitude());
    }

    public function testPhotoTrackCoords(): void
    {
        $exif = $this->createMock(Exif::class);
        $exif->method('getCreationDate')->willReturn(new \DateTime('2019-06-24 19:25:00'));

        $exifWrapper = $this->createMock(ExifWrapper::class);
        $exifWrapper->method('getExifData')->willReturn($exif);

        $trackReader = $this->createMock(TrackReader::class);

        $loop = $this->createMock(Loop::class);
        $loop->method('setPositionList')->will($this->returnSelf());
        $loop->method('searchPositionForDateTime')->willReturn(new Position(52.268021, 10.500126));

        $photoGps = new PhotoGps($trackReader, $exifWrapper, $loop);

        $track = $this->createMock(Track::class);

        $photo = new Photo();

        $photoGps
            ->setPhoto($photo)
            ->setTrack($track)
            ->execute();

        $this->assertTrue($photo->hasCoordinates());
        $this->assertEquals(52.268021, $photo->getLatitude());
        $this->assertEquals(10.500126, $photo->getLongitude());
    }
}
