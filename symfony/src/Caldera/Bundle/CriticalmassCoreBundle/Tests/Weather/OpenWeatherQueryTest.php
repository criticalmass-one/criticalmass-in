<?php

namespace Caldera\CriticalmassOpenweatherBundle\Tests\Weather;

use Caldera\CriticalmassCoreBundle\Entity\Ride;
use Caldera\CriticalmassOpenweatherBundle\Utility\Weather\OpenWeatcherQuery;

use Caldera\CriticalmassOpenweatherBundle\Utility\Weather\OpenWeatherReader;
use PHPUnit_Framework_TestCase;

class OpenWeatherQueryTest extends PHPUnit_Framework_TestCase {
    protected $testRide;
    
    protected function setup()
    {
        $this->testRide = new Ride();
        $this->testRide->setLatitude(53.550556);
        $this->testRide->setLongitude(9.993333);
    }

    public function testQuery()
    {
        $owq = new OpenWeatcherQuery();

        $owq->setRide($this->testRide);

        $json = $owq->execute();

        $this->assertNotEmpty($json);
    }

    public function testQueryResult()
    {
        $owq = new OpenWeatcherQuery();

        $owq->setRide($this->testRide);

        $json = $owq->execute();

        $object = json_decode($json);

        $this->assertNotNull($object);
        $this->assertObjectHasAttribute('city', $object);
        $this->assertObjectHasAttribute('list', $object);
    }

    public function testQueryEntity()
    {
        $owq = new OpenWeatcherQuery();

        $owq->setRide($this->testRide);

        $json = $owq->execute();

        $owr = new OpenWeatherReader();
        $owr->setDate(new \DateTime());
        $owr->setJson($json);
        
        $entity = $owr->createEntity();

        $this->assertNotNull($entity);
        $this->assertNotNull($entity->getTemperatureDay());
    }
}