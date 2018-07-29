<?php

namespace Caldera\GeoBundle\Test\DistanceCalculator;

use Caldera\GeoBundle\Entity\Position;
use Caldera\GeoBundle\GpxWriter\GpxWriter;
use Caldera\GeoBundle\PositionList\PositionList;
use PHPUnit\Framework\TestCase;

class PositionListTest extends TestCase
{
    public function testPositionList1()
    {
        $positionList = new PositionList();

        $this->assertEquals(0, $positionList->count());
    }

    public function testPositionList2()
    {
        $positionList = new PositionList();

        $positionList
            ->add(new Position(57, 10))
            ->add(new Position(57, 9))
        ;

        $this->assertEquals(2, $positionList->count());
    }

    public function testPositionList3()
    {
        $positionList = new PositionList();

        $positionList
            ->push(new Position(57, 10))
            ->push(new Position(57, 9))
        ;

        $this->assertEquals(2, $positionList->count());
    }
}
