<?php declare(strict_types=1);

namespace Tests\PhotoGps;

use App\Criticalmass\Image\PhotoGps\PhotoGpsInterface;
use App\Entity\City;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\PhotoGps\Mocks\GpsPhoto;
use Tests\PhotoGps\Mocks\MockTrack;
use Tests\PhotoGps\Mocks\NoGpsPhoto;

/**
 * Integration test that requires photos to be in public/photos directory.
 * Use PhotoGpsTest for unit tests with mocks instead.
 */
class GpsTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->markTestSkipped('Integration test requires test photos in public/photos directory. Use PhotoGpsTest instead.');
        self::bootKernel();
    }

    protected function getPhotoGps(): PhotoGpsInterface
    {
        return static::getContainer()->get(PhotoGpsInterface::class);
    }

    public function testPhotoWithoutCoords(): void
    {
        $photo = new NoGpsPhoto();

        $this->getPhotoGps()->setPhoto($photo)->execute();

        $this->assertFalse($photo->hasCoordinates());
        $this->assertNull($photo->getLatitude());
        $this->assertNull($photo->getLongitude());
    }

    public function testPhotoWithCoords(): void
    {
        $photo = new GpsPhoto();

        $this->getPhotoGps()->setPhoto($photo)->execute();

        $this->assertTrue($photo->hasCoordinates());
        $this->assertEquals(52.266666666667, $photo->getLatitude());
        $this->assertEquals(10.5, $photo->getLongitude());
    }

    public function testPhotoTrackCoords(): void
    {
        $photo = new NoGpsPhoto();
        $track = new MockTrack();

        $this->getPhotoGps()->setPhoto($photo)->setDateTimeZone(new \DateTimeZone('Europe/Berlin'))->setTrack($track)->execute();

        $this->assertTrue($photo->hasCoordinates());
        $this->assertEquals(52.268021, $photo->getLatitude());
        $this->assertEquals(10.500126, $photo->getLongitude());
    }

    public function testPhotoTrackCoordsAutoTimezone(): void
    {
        $city = new City();
        $city->setTimezone('Europe/Berlin');

        $photo = new NoGpsPhoto();
        $photo->setCity($city);

        $track = new MockTrack();

        $this->getPhotoGps()->setPhoto($photo)->setTrack($track)->execute();

        $this->assertTrue($photo->hasCoordinates());
        $this->assertEquals(52.268021, $photo->getLatitude());
        $this->assertEquals(10.500126, $photo->getLongitude());
    }
}