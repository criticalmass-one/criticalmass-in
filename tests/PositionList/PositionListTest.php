<?php declare(strict_types=1);

namespace Tests\DistanceCalculator;

use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\PositionList\PositionList;
use PHPUnit\Framework\TestCase;

class PositionListTest extends TestCase
{
    public function testPositionList1(): void
    {
        $positionList = new PositionList();

        $this->assertEquals(0, $positionList->count());
    }

    public function testPositionList2(): void
    {
        $positionList = new PositionList();

        $positionList
            ->add(new Position(57, 10))
            ->add(new Position(57, 9))
        ;

        $this->assertEquals(2, $positionList->count());
    }

    public function testPositionList3(): void
    {
        $positionList = new PositionList();

        $positionList
            ->push(new Position(57, 10))
            ->push(new Position(57, 9))
        ;

        $this->assertEquals(2, $positionList->count());
    }
}
