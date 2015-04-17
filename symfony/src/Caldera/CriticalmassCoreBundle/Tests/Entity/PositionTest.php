<?php

namespace Caldera\CriticalmassCoreBundle\Tests\Entity;

use Caldera\CriticalmassCoreBundle\Entity\Position;
use PHPUnit_Framework_TestCase;

class PositionTest extends PHPUnit_Framework_TestCase {

    public function testEuqalPositions()
    {
        $position1 = new Position();
        $position1->setLatitude(0.0);
        $position1->setLongitude(0.0);
        
        $position2 = new Position();
        $position2->setLatitude(0.0);
        $position2->setLongitude(0.0);

        $position3 = new Position();
        $position3->setLatitude(1.0);
        $position3->setLongitude(1.0);

        $this->assertTrue($position1->isEqual($position2));
        $this->assertFalse($position2->isEqual($position3));
        $this->assertFalse($position3->isEqual($position1));

    }
} 