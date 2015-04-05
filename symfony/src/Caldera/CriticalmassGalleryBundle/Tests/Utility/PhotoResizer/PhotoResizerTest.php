<?php

namespace Caldera\CriticalmassGalleryBundle\Tests\Utility\PhotoResizer;

use Caldera\CriticalmassGalleryBundle\Utility\PhotoResizer\PhotoResizer;

class PhotoResizerTest extends \PHPUnit_Framework_TestCase {

    public static function tearDownAfterClass()
    {
        unlink(getcwd().'/../src/Caldera/CriticalmassGalleryBundle/Resources/public/images/testphoto2.jpeg');
        unlink(getcwd().'/../src/Caldera/CriticalmassGalleryBundle/Resources/public/images/testphoto3.jpeg');
    }
    
    public function testCurrentSize()
    {
        $filename = getcwd().'/../src/Caldera/CriticalmassGalleryBundle/Resources/public/images/testphoto.jpeg';

        $pr = new PhotoResizer();

        $pr->loadJpeg($filename);

        $size = $pr->getCurrentSize();

        $this->assertEquals(640, $size[0]);
        $this->assertEquals(480, $size[1]);
    }

    public function testResizeAbsolute()
    {
        $filename = getcwd().'/../src/Caldera/CriticalmassGalleryBundle/Resources/public/images/testphoto.jpeg';

        $pr = new PhotoResizer();

        $pr->loadJpeg($filename);

        $pr->resize(320, 240);

        $pr->saveJpeg(getcwd().'/../src/Caldera/CriticalmassGalleryBundle/Resources/public/images/testphoto2.jpeg');

        $size = getimagesize(getcwd().'/../src/Caldera/CriticalmassGalleryBundle/Resources/public/images/testphoto2.jpeg');

        $this->assertEquals(320, $size[0]);
        $this->assertEquals(240, $size[1]);
    }

    public function testResizeFactor()
    {
        $filename = getcwd().'/../src/Caldera/CriticalmassGalleryBundle/Resources/public/images/testphoto.jpeg';

        $pr = new PhotoResizer();

        $pr->loadJpeg($filename);

        $pr->resizeFactor(0.25);

        $pr->saveJpeg(getcwd().'/../src/Caldera/CriticalmassGalleryBundle/Resources/public/images/testphoto3.jpeg');

        $size = getimagesize(getcwd().'/../src/Caldera/CriticalmassGalleryBundle/Resources/public/images/testphoto3.jpeg');

        $this->assertEquals(160, $size[0]);
        $this->assertEquals(120, $size[1]);
    }
}