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
            ->searchIndexForDateTime(new \DateTime('2011-06-24 19:35:00'));

        $this->assertEquals(4, $actualIndex2);

        $actualIndex3 = $loop
            ->setPositionList($this->createPositionList())
            ->searchIndexForDateTime(new \DateTime('2011-06-24 19:40:00'));

        $this->assertEquals(4, $actualIndex3);
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