<?php

namespace Caldera\GeoBundle\Test\DistanceCalculator;

use Caldera\GeoBasic\Coord\Coord;
use Caldera\GeoBasic\Track\Track;
use Caldera\GeoBundle\DistanceCalculator\DistanceCalculator;
use Caldera\GeoBundle\Entity\Position;
use Caldera\GeoBundle\PositionList\PositionList;
use Caldera\GeoBundle\PositionList\PositionListInterface;
use PHPUnit\Framework\TestCase;

class SimpleDistanceCalculatorTest extends TestCase
{
    protected function createPositionList(): PositionListInterface
    {
        $positionList = new PositionList();

        $positionList
            ->add(new Position(53.550556, 9.993333))
            ->add(new Position(52.518611, 13.408333))
        ;

        return $positionList;
    }

    public function testDistanceCalculator1()
    {
        $distanceCalculator = new DistanceCalculator();

        $distance = $distanceCalculator
            ->setPositionList($this->createPositionList())
            ->calculate()
        ;

        $this->assertEquals(269.83697059097, $distance);
    }
}
