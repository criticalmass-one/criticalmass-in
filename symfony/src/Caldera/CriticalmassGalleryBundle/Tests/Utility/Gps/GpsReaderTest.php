<?php
/**
 * Created by IntelliJ IDEA.
 * User: malte
 * Date: 04.02.15
 * Time: 17:35
 */

namespace Caldera\CriticalmassGalleryBundle\Tests\Utility\Gps;


use Caldera\CriticalmassGalleryBundle\Utility\Gps\GpsReader;

class GpsReaderTest extends \PHPUnit_Framework_TestCase {
    
    public function testPhotoGps()
    {
        $gr = new GpsReader();
        $gr->setFilename(getcwd().'/src/Caldera/CriticalmassGalleryBundle/Resources/public/images/testphoto.jpeg');
        $gps = $gr->execute();

        $this->assertEquals(53.57, round($gps['latitude'], 2));
        $this->assertEquals(9.92, round($gps['longitude'], 2));
    }

}