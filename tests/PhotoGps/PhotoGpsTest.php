<?php declare(strict_types=1);

namespace Tests\PhotoGps;

use App\Criticalmass\Gps\GpxReader\TrackReader;
use App\Criticalmass\Image\ExifWrapper\ExifWrapper;
use App\Criticalmass\Image\PhotoGps\PhotoGps;
use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Track;
use League\Flysystem\Filesystem;
use PHPExif\Exif;
use PHPUnit\Framework\TestCase;
use Tests\PhotoGps\Mocks\GpsPhoto;
use Tests\PhotoGps\Mocks\MockTrack;
use Tests\PhotoGps\Mocks\NoGpsPhoto;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class PhotoGpsTest extends TestCase
{
    public function testPhotoWithoutCoords(): void
    {
        $uploadHelper = $this->createMock(UploaderHelper::class);
        $trackReader = $this->createMock(TrackReader::class);
        $filesystem = $this->createMock(Filesystem::class);
        $exifWrapper = $this->createMock(ExifWrapper::class);

        $photoGps = new PhotoGps($uploadHelper, $trackReader, $filesystem, $exifWrapper);

        $photo = $this->createMock(Photo::class);

        $photoGps->setPhoto($photo)->execute();

        $this->assertFalse($photo->hasCoordinates());
        $this->assertNull($photo->getLatitude());
        $this->assertNull($photo->getLongitude());
    }

    public function testPhotoWithCoords(): void
    {
        $uploadHelper = $this->createMock(UploaderHelper::class);
        $trackReader = $this->createMock(TrackReader::class);
        $filesystem = $this->createMock(Filesystem::class);
        $exifWrapper = $this->createMock(ExifWrapper::class);
        $exif = $this->createMock(Exif::class);

        $exif->method('getGPS')->willReturn('52.266666666667,10.5');
        $exifWrapper->method('getExifData')->willReturn($exif);

        $photoGps = new PhotoGps($uploadHelper, $trackReader, $filesystem, $exifWrapper);

        $photo = new Photo();

        $photoGps->setPhoto($photo)->execute();

        $this->assertTrue($photo->hasCoordinates());
        $this->assertEquals(52.266666666667, $photo->getLatitude());
        $this->assertEquals(10.5, $photo->getLongitude());
    }

    public function testPhotoTrackCoords(): void
    {
        $uploadHelper = $this->createMock(UploaderHelper::class);
        $filesystem = $this->createMock(Filesystem::class);
        $exifWrapper = $this->createMock(ExifWrapper::class);
        $exif = $this->createMock(Exif::class);

        $trackReader = $this->createMock(TrackReader::class);
        $trackReader->method('findCoordNearDateTime')->willReturn(['latitude' => 52.268021, 'longitude' => 10.500126]);

        $exif->method('getCreationDate')->willReturn(new \DateTime());
        $exifWrapper->method('getExifData')->willReturn($exif);

        $photoGps = new PhotoGps($uploadHelper, $trackReader, $filesystem, $exifWrapper);

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

    public function testPhotoTrackCoordsAutoTimezone(): void
    {
        $uploadHelper = $this->createMock(UploaderHelper::class);
        $filesystem = $this->createMock(Filesystem::class);
        $exifWrapper = $this->createMock(ExifWrapper::class);
        $exif = $this->createMock(Exif::class);

        $trackReader = $this->createMock(TrackReader::class);
        $trackReader->method('findCoordNearDateTime')->willReturn(['latitude' => 52.268021, 'longitude' => 10.500126]);

        $exif->method('getCreationDate')->willReturn(new \DateTime());
        $exifWrapper->method('getExifData')->willReturn($exif);

        $photoGps = new PhotoGps($uploadHelper, $trackReader, $filesystem, $exifWrapper);

        $track = $this->createMock(Track::class);

        $city = $this->createMock(City::class);
        $city->method('getTimezone')->willReturn('Europe/Berlin');

        $photo = new Photo();
        $photo->setCity($city);
        
        $photoGps
            ->setPhoto($photo)
            ->setTrack($track)
            ->execute();

        $this->assertTrue($photo->hasCoordinates());
        $this->assertEquals(52.268021, $photo->getLatitude());
        $this->assertEquals(10.500126, $photo->getLongitude());
    }
}
