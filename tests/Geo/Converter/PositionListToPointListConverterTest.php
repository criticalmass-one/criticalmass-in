<?php declare(strict_types=1);

namespace Tests\Geo\Converter;

use App\Criticalmass\Geo\Converter\PositionListToPointListConverter;
use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\PositionList\PositionList;
use PHPUnit\Framework\TestCase;

class PositionListToPointListConverterTest extends TestCase
{
    public function testEmptyPositionList(): void
    {
        $positionList = new PositionList();

        $actualPointList = PositionListToPointListConverter::convert($positionList);

        $expectedPointList = [];

        $this->assertEquals($expectedPointList, $actualPointList);
    }

    public function testPositionListConverterWithOnePosition(): void
    {
        $positionList = new PositionList();
        $positionList
            ->add(new Position(53.5, 10.5));

        $actualPointList = PositionListToPointListConverter::convert($positionList);

        $expectedPointList = [
            [53.5, 10.5],
        ];

        $this->assertEquals($expectedPointList, $actualPointList);
    }

    public function testPositionListConverterWithFiveIdenticalPosition(): void
    {
        $positionList = new PositionList();
        $positionList
            ->add(new Position(53.5, 10.5))
            ->add(new Position(53.5, 10.5))
            ->add(new Position(53.5, 10.5))
            ->add(new Position(53.5, 10.5))
            ->add(new Position(53.5, 10.5));

        $actualPointList = PositionListToPointListConverter::convert($positionList);

        $expectedPointList = [
            [53.5, 10.5],
            [53.5, 10.5],
            [53.5, 10.5],
            [53.5, 10.5],
            [53.5, 10.5],
        ];

        $this->assertEquals($expectedPointList, $actualPointList);
    }

    public function testPositionListConverterWithFiveDifferentPosition(): void
    {
        $positionList = new PositionList();
        $positionList
            ->add(new Position(53.5, 10.5))
            ->add(new Position(54.5, 11.5))
            ->add(new Position(55.5, 12.5))
            ->add(new Position(56.5, 13.5))
            ->add(new Position(57.5, 14.5));

        $actualPointList = PositionListToPointListConverter::convert($positionList);

        $expectedPointList = [
            [53.5, 10.5],
            [54.5, 11.5],
            [55.5, 12.5],
            [56.5, 13.5],
            [57.5, 14.5],
        ];

        $this->assertEquals($expectedPointList, $actualPointList);
    }

    public function testPositionListConverterWithFiveDifferentDateTimeAltitudePosition(): void
    {
        $positionList = new PositionList();
        $positionList
            ->add((new Position(53.5, 10.5))->setDateTime(new \DateTime('2011-06-24 19:00:00'))->setAltitude(5.5))
            ->add((new Position(54.5, 11.5))->setDateTime(new \DateTime('2011-06-24 19:10:00'))->setAltitude(6.5))
            ->add((new Position(55.5, 12.5))->setDateTime(new \DateTime('2011-06-24 19:20:00'))->setAltitude(7.5))
            ->add((new Position(56.5, 13.5))->setDateTime(new \DateTime('2011-06-24 19:30:00'))->setAltitude(8.5))
            ->add((new Position(57.5, 14.5))->setDateTime(new \DateTime('2011-06-24 19:40:00'))->setAltitude(9.5));

        $actualPointList = PositionListToPointListConverter::convert($positionList);

        $expectedPointList = [
            [53.5, 10.5],
            [54.5, 11.5],
            [55.5, 12.5],
            [56.5, 13.5],
            [57.5, 14.5],
        ];

        $this->assertEquals($expectedPointList, $actualPointList);
    }
}
