<?php

namespace Caldera\CriticalmassGalleryBundle\Tests\Utility\Gps;

use Caldera\CriticalmassGalleryBundle\Tests\Utility\Gps\Entity\TestPhoto;
use Caldera\CriticalmassGalleryBundle\Utility\ExifReader\GpsReader;

class GpsReaderTest extends \PHPUnit_Framework_TestCase
{
    protected $photo;

    public function testPhotoGps()
    {
        $gr = new GpsReader($this->photo);
        $gr->execute();

        $this->assertEquals(53.57, round($gr->getLatitude(), 2));
        $this->assertEquals(9.92, round($gr->getLongitude(), 2));
    }

    protected function setup()
    {
        $this->photo = new TestPhoto();
        $this->photo->setFilePath(getcwd() . '/../src/Caldera/CriticalmassGalleryBundle/Resources/public/images/testphoto.jpeg');
    }
}


