<?php

namespace Caldera\CriticalmassCoreBundle\Tests\Entity;

use Caldera\CriticalmassCoreBundle\Entity\City;
use Caldera\CriticalmassCoreBundle\Entity\Ride;
use PHPUnit_Framework_TestCase;

class CityTest extends PHPUnit_Framework_TestCase {

    protected $testCity;

    protected function setUp()
    {
        $ride1 = new Ride();
        $ride1->setDateTime(new \DateTime('2014-01-01 18:00:00'));
        $ride1->setTitle('Ride 1');
        $ride1->setEstimatedParticipants(1000);

        $ride2DateTime = new \DateTime();
        $ride2Offset = new \DateInterval('P1D');
        $ride2DateTime->add($ride2Offset);
        
        $ride2 = new Ride();
        $ride2->setDateTime($ride2DateTime);
        $ride2->setTitle('Ride 2');
        $ride2->setEstimatedParticipants(2000);
        
        $ride3DateTime = new \DateTime();
        $ride3Offset = new \DateInterval('P2D');
        $ride3DateTime->add($ride3Offset);
        
        $ride3 = new Ride();
        $ride3->setDate($ride3DateTime);
        $ride3->setIsArchived(true);
        $ride3->setTitle('Ride 3');
        $ride3->setEstimatedParticipants(3000);
        
        $this->testCity = new City();
        
        $this->testCity->addRide($ride1);
        $this->testCity->addRide($ride2);
        $this->testCity->addRide($ride3);
    }

    public function testCurrentRide()
    {
        $currentRide = $this->testCity->getCurrentRide();

        $this->assertEquals('Ride 2', $currentRide->getTitle());
    }
    
    public function testHasRideInMonth()
    {
        $dateTime1 = new \DateTime('2014-01-01 18:00:00');
        $this->assertTrue($this->testCity->hasRideInMonth($dateTime1));
        
        $dateTime2 = new \DateTime('2014-01-08 18:00:00');
        $this->assertTrue($this->testCity->hasRideInMonth($dateTime2));
        
        $dateTime3 = new \DateTime('2013-12-01 18:00:00');
        $this->assertFalse($this->testCity->hasRideInMonth($dateTime3));
    }
    
    public function testHasRideAtMonthDay()
    {
        $dateTime1 = new \DateTime('2014-01-01 18:00:00');
        $this->assertTrue($this->testCity->hasRideAtMonthDay($dateTime1));

        $dateTime2 = new \DateTime('2013-12-01 18:00:00');
        $this->assertFalse($this->testCity->hasRideAtMonthDay($dateTime2));
    }
    
    public function testCalculateAverageRideParticipants()
    {
        $this->assertEquals(2000, $this->testCity->calculateAverageRideParticipants());
    }
} 