<?php declare(strict_types=1);

namespace Tests\DistanceCalculator;

use App\Criticalmass\Geo\DistanceCalculator\DistanceCalculator;
use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\PositionList\PositionList;
use App\Criticalmass\Geo\PositionList\PositionListInterface;
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

    public function testDistanceCalculator1(): void
    {
        $distanceCalculator = new DistanceCalculator();

        $distance = $distanceCalculator
            ->setPositionList($this->createPositionList())
            ->calculate()
        ;

        $this->assertEquals(269.83697059097, $distance);
    }
}
