<?php declare(strict_types=1);

namespace Tests\Geo\PositionList;

use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\PositionList\PositionList;
use PHPUnit\Framework\TestCase;

class PositionListTest extends TestCase
{
    public function testEmptyPositionList(): void
    {
        $positionList = new PositionList();

        $this->assertEquals(0, $positionList->count());
    }

    public function testPositionListAdd(): void
    {
        $positionList = new PositionList();

        $positionList
            ->add(new Position(57, 10))
            ->add(new Position(57, 9));

        $this->assertEquals(2, $positionList->count());
    }

    public function testPositionListPush(): void
    {
        $positionList = new PositionList();

        $positionList
            ->push(new Position(57, 10))
            ->push(new Position(57, 9));

        $this->assertEquals(2, $positionList->count());
    }
}
