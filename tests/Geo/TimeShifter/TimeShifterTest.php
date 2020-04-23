<?php declare(strict_types=1);

namespace Tests\TimeShifter;

use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\PositionList\PositionList;
use App\Criticalmass\Geo\TimeShifter\TimeShifter;
use PHPUnit\Framework\TestCase;

class TimeShifterTest extends TestCase
{
    protected function createPositionList(): PositionList
    {
        $position1 = new Position(53, 10);
        $position1->setTimestamp((int) (new \DateTime('2011-06-24 19:00:00'))->format('U'));

        $position2 = new Position(54, 11);
        $position2->setTimestamp((int) (new \DateTime('2011-06-24 19:15:00'))->format('U'));

        $positionList = new PositionList();
        $positionList
            ->add($position1)
            ->add($position2);

        return $positionList;
    }

    public function testTimeShifterNoop(): void
    {
        $positionList = $this->createPositionList();

        $position1 = $positionList->get(0);
        $position2 = $positionList->get(1);

        $this->assertEquals((new \DateTime('2011-06-24 19:00:00'))->format('U'), $position1->getTimestamp());
        $this->assertEquals((new \DateTime('2011-06-24 19:15:00'))->format('U'), $position2->getTimestamp());
    }

    public function testTimeShifterOneHour(): void
    {
        $timeShifter = new TimeShifter();
        $timeShifter->setPositionList($this->createPositionList());

        $timeShifter->shift(new \DateInterval('PT1H'));

        $positionList = $timeShifter->getPositionList();

        $position1 = $positionList->get(0);
        $position2 = $positionList->get(1);

        $this->assertEquals((new \DateTime('2011-06-24 20:00:00'))->format('U'), $position1->getTimestamp());
        $this->assertEquals((new \DateTime('2011-06-24 20:15:00'))->format('U'), $position2->getTimestamp());
    }
}
