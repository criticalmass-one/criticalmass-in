<?php

namespace Caldera\CriticalmassOpenweatherBundle\Tests\Weather;

use Caldera\CriticalmassOpenweatherBundle\Utility\Weather\OpenWeatherReader;
use PHPUnit_Framework_TestCase;

class OpenWeatherReaderTest extends PHPUnit_Framework_TestCase {

    private $weatherString;
    
    public function setup()
    {
        $this->weatherString = '{"cod":"200","message":0.0334,"city":{"id":2959681,"name":"Achim","coord":{"lon":9.0263,"lat":53.01416},"country":"DE","population":0},"cnt":10,"list":[{"dt":1423047600,"temp":{"day":-0.73,"min":-6.56,"max":-0.56,"night":-6.56,"eve":-3.61,"morn":-0.73},"pressure":1025.03,"humidity":100,"weather":[{"id":800,"main":"Clear","description":"klarer Himmel","icon":"01d"}],"speed":2.86,"deg":352,"clouds":0},{"dt":1423134000,"temp":{"day":-1.1,"min":-6.6,"max":-0.12,"night":-4.11,"eve":-0.82,"morn":-6.6},"pressure":1033.03,"humidity":100,"weather":[{"id":802,"main":"Clouds","description":"überwiegend bewölkt","icon":"03d"}],"speed":2.47,"deg":31,"clouds":32},{"dt":1423220400,"temp":{"day":-3.24,"min":-7.7,"max":-1.6,"night":-7.7,"eve":-5.19,"morn":-6.63},"pressure":1044.45,"humidity":100,"weather":[{"id":801,"main":"Clouds","description":"ein paar Wolken","icon":"02d"}],"speed":2.58,"deg":73,"clouds":12},{"dt":1423306800,"temp":{"day":-0.31,"min":-5.68,"max":3.78,"night":2.05,"eve":3.78,"morn":-5.68},"pressure":1038.85,"humidity":95,"weather":[{"id":804,"main":"Clouds","description":"wolkenbedeckt","icon":"04d"}],"speed":5.91,"deg":252,"clouds":92},{"dt":1423393200,"temp":{"day":3.9,"min":-1.18,"max":5.04,"night":4.78,"eve":5.04,"morn":-1.18},"pressure":1033.88,"humidity":0,"weather":[{"id":500,"main":"Rain","description":"leichter Regen","icon":"10d"}],"speed":6.09,"deg":266,"clouds":100,"rain":1.76},{"dt":1423479600,"temp":{"day":7.01,"min":4.23,"max":7.01,"night":6.09,"eve":6.22,"morn":4.23},"pressure":1028.92,"humidity":0,"weather":[{"id":500,"main":"Rain","description":"leichter Regen","icon":"10d"}],"speed":5.58,"deg":307,"clouds":47,"rain":1.92},{"dt":1423566000,"temp":{"day":7.21,"min":5.44,"max":7.21,"night":5.44,"eve":6.38,"morn":5.65},"pressure":1025.54,"humidity":0,"weather":[{"id":500,"main":"Rain","description":"leichter Regen","icon":"10d"}],"speed":8.06,"deg":262,"clouds":66,"rain":1.2},{"dt":1423652400,"temp":{"day":6.67,"min":4.01,"max":6.67,"night":4.01,"eve":5.01,"morn":5.92},"pressure":1008.62,"humidity":0,"weather":[{"id":501,"main":"Rain","description":"mäßiger Regen","icon":"10d"}],"speed":12.17,"deg":264,"clouds":94,"rain":7.63},{"dt":1423738800,"temp":{"day":5.39,"min":2.85,"max":5.39,"night":2.85,"eve":3.31,"morn":3.88},"pressure":1005.5,"humidity":0,"weather":[{"id":501,"main":"Rain","description":"mäßiger Regen","icon":"10d"}],"speed":10.36,"deg":266,"clouds":9,"rain":8.61},{"dt":1423825200,"temp":{"day":4.7,"min":-0.67,"max":4.7,"night":-0.67,"eve":3.17,"morn":1.84},"pressure":1020.42,"humidity":0,"weather":[{"id":500,"main":"Rain","description":"leichter Regen","icon":"10d"}],"speed":6.14,"deg":265,"clouds":82,"rain":1.75}]}';
    }
    
    public function testTemperature()
    {
        $owr = new OpenWeatherReader();
        
        $owr->setJson($this->weatherString);
        $owr->setDate(new \DateTime('2015-02-04 00:00:00'));
        
        $entity = $owr->createEntity();

        $this->assertEquals(0, $entity->getClouds());
    }
}