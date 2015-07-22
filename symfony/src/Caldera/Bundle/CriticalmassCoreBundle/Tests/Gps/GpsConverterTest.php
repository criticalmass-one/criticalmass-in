<?php

namespace Caldera\CriticalmassGalleryBundle\Tests\Utility\Gps;

use Caldera\CriticalmassGalleryBundle\Utility\Gps\GpsConverter;

class GpsConverterTest extends \PHPUnit_Framework_TestCase {
    
    public function testGpsConvertion()
    {
        $latitude = array('53/1', '33/1', '5588/100');
        $longitude = array('9/1', '55/1', '1314/100');
        
        $gc = new GpsConverter();
        
        $this->assertEquals(53.57, round($gc->convert($latitude), 2));
        $this->assertEquals(9.92, round($gc->convert($longitude), 2));
    }
}