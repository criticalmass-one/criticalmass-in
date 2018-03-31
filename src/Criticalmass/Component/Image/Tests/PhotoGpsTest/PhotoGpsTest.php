<?php declare(strict_types=1);

namespace Criticalmass\Component\Util\Tests;

use Criticalmass\Component\Image\PhotoGps\PhotoGps;
use Criticalmass\Component\Image\PhotoGps\PhotoGpsInterface;
use Criticalmass\Component\Image\Tests\PhotoGpsTest\Mocks\GpsPhoto;
use Criticalmass\Component\Image\Tests\PhotoGpsTest\Mocks\MockTrack;
use Criticalmass\Component\Image\Tests\PhotoGpsTest\Mocks\NoGpsPhoto;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PhotoGpsTest extends KernelTestCase
{
    protected function setUp()
    {
        self::bootKernel();
    }

    protected function getPhotoGps(): PhotoGpsInterface
    {
        return static::$kernel->getContainer()->get(PhotoGps::class);
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

        $this->getPhotoGps()->setPhoto($photo)->setTrack($track)->execute();

        $this->assertTrue($photo->hasCoordinates());
        $this->assertEquals(52.26329, $photo->getLatitude());
        $this->assertEquals(10.527089, $photo->getLongitude());
    }
}
