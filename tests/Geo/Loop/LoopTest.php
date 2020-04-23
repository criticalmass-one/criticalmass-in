<?php declare(strict_types=1);

namespace Tests\Geo\Loop;

use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\Loop\Loop;
use App\Criticalmass\Geo\PositionList\PositionList;
use App\Criticalmass\Geo\PositionList\PositionListInterface;
use PHPUnit\Framework\TestCase;

class LoopTest extends TestCase
{
    public function testEmptyLoop(): void
    {
        $loop = new Loop();

        $actualIndex = $loop->searchIndexForDateTime(new \DateTime('2011-06-24 19:00:00'));

        $this->assertNull($actualIndex);
    }

    public function testLoopWithPositionListIndex(): void
    {
        $loop = new Loop();

        $actualIndex1 = $loop
            ->setPositionList($this->createPositionList())
            ->searchIndexForDateTime(new \DateTime('2011-06-24 19:30:00'));

        $this->assertEquals(3, $actualIndex1);

        $actualIndex2 = $loop
            ->setPositionList($this->createPositionList())
            ->searchIndexForDateTime(new \DateTime('2011-06-24 19:31:00'));

        $this->assertEquals(4, $actualIndex2);

        $actualIndex3 = $loop
            ->setPositionList($this->createPositionList())
            ->searchIndexForDateTime(new \DateTime('2011-06-24 19:32:00'));

        $this->assertEquals(4, $actualIndex3);

        $actualIndex4 = $loop
            ->setPositionList($this->createPositionList())
            ->searchIndexForDateTime(new \DateTime('2011-06-24 19:33:00'));

        $this->assertEquals(4, $actualIndex4);

        $actualIndex5 = $loop
            ->setPositionList($this->createPositionList())
            ->searchIndexForDateTime(new \DateTime('2011-06-24 19:34:00'));

        $this->assertEquals(4, $actualIndex5);

        $actualIndex6 = $loop
            ->setPositionList($this->createPositionList())
            ->searchIndexForDateTime(new \DateTime('2011-06-24 19:35:00'));

        $this->assertEquals(4, $actualIndex6);

        $actualIndex7 = $loop
            ->setPositionList($this->createPositionList())
            ->searchIndexForDateTime(new \DateTime('2011-06-24 19:36:00'));

        $this->assertEquals(4, $actualIndex7);

        $actualIndex8 = $loop
            ->setPositionList($this->createPositionList())
            ->searchIndexForDateTime(new \DateTime('2011-06-24 19:37:00'));

        $this->assertEquals(4, $actualIndex8);

        $actualIndex9 = $loop
            ->setPositionList($this->createPositionList())
            ->searchIndexForDateTime(new \DateTime('2011-06-24 19:38:00'));

        $this->assertEquals(4, $actualIndex9);

        $actualIndex10 = $loop
            ->setPositionList($this->createPositionList())
            ->searchIndexForDateTime(new \DateTime('2011-06-24 19:39:00'));

        $this->assertEquals(4, $actualIndex10);

        $actualIndex11 = $loop
            ->setPositionList($this->createPositionList())
            ->searchIndexForDateTime(new \DateTime('2011-06-24 19:40:00'));

        $this->assertEquals(4, $actualIndex11);
    }

    public function testLoopWithPositionListPositions(): void
    {
        $loop = new Loop();

        $actualPosition1 = $loop
            ->setPositionList($this->createPositionList())
            ->searchPositionForDateTime(new \DateTime('2011-06-24 19:30:00'));

        $expectedPosition1 = (new Position(13.5, 17.5))->setDateTime(new \DateTime('2011-06-24 19:30:00'));

        $this->assertEquals($expectedPosition1, $actualPosition1);

        $actualPosition2 = $loop
            ->setPositionList($this->createPositionList())
            ->searchPositionForDateTime(new \DateTime('2011-06-24 19:35:00'));

        $expectedPosition2 = (new Position(14.5, 16.5))->setDateTime(new \DateTime('2011-06-24 19:40:00'));

        $this->assertEquals($expectedPosition2, $actualPosition2);

        $actualPosition3 = $loop
            ->setPositionList($this->createPositionList())
            ->searchPositionForDateTime(new \DateTime('2011-06-24 19:40:00'));

        $expectedPosition3 = (new Position(14.5, 16.5))->setDateTime(new \DateTime('2011-06-24 19:40:00'));

        $this->assertEquals($expectedPosition3, $actualPosition3);
    }

    public function testLoopWithPositionListAndOutboundDateTime(): void
    {
        $loop = new Loop();

        $actualIndex1 = $loop
            ->setPositionList($this->createPositionList())
            ->searchIndexForDateTime(new \DateTime('2011-06-24 18:00:00'));

        $this->assertEquals(0, $actualIndex1);

        $actualIndex2 = $loop
            ->setPositionList($this->createPositionList())
            ->searchIndexForDateTime(new \DateTime('2011-06-24 22:00:00'));

        $this->assertEquals(10, $actualIndex2);
    }

    protected function createPositionList(): PositionListInterface
    {
        $positionList = new PositionList();
        $positionList
            ->add((new Position(10.5, 20.5))->setDateTime(new \DateTime('2011-06-24 19:00:00')))
            ->add((new Position(11.5, 19.5))->setDateTime(new \DateTime('2011-06-24 19:10:00')))
            ->add((new Position(12.5, 18.5))->setDateTime(new \DateTime('2011-06-24 19:20:00')))
            ->add((new Position(13.5, 17.5))->setDateTime(new \DateTime('2011-06-24 19:30:00')))
            ->add((new Position(14.5, 16.5))->setDateTime(new \DateTime('2011-06-24 19:40:00')))
            ->add((new Position(15.5, 15.5))->setDateTime(new \DateTime('2011-06-24 19:50:00')))
            ->add((new Position(16.5, 14.5))->setDateTime(new \DateTime('2011-06-24 20:00:00')))
            ->add((new Position(17.5, 13.5))->setDateTime(new \DateTime('2011-06-24 20:10:00')))
            ->add((new Position(18.5, 12.5))->setDateTime(new \DateTime('2011-06-24 20:20:00')))
            ->add((new Position(19.5, 11.5))->setDateTime(new \DateTime('2011-06-24 20:30:00')))
            ->add((new Position(20.5, 10.5))->setDateTime(new \DateTime('2011-06-24 20:40:00')));

        return $positionList;
    }
}