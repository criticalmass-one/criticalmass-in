<?php declare(strict_types=1);

namespace Tests\Geo\Converter;

use App\Criticalmass\Geo\Converter\PositionListToJsonConverter;
use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\PositionList\PositionList;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;

class PositionListToJsonConverterTest extends TestCase
{
    public function testEmptyPositionList(): void
    {
        $positionList = new PositionList();

        $converter = new PositionListToJsonConverter($this->createSerializer());

        $actualPointList = $converter->convert($positionList);

        $expectedPointList = '[]';

        $this->assertEquals($expectedPointList, $actualPointList);
    }

    public function testPositionListWithLatLngPositions(): void
    {
        $positionList = new PositionList();
        $positionList
            ->add(new Position(53.5, 10.5))
            ->add(new Position(53.6, 10.6));

        $converter = new PositionListToJsonConverter($this->createSerializer());

        $actualPointList = $converter->convert($positionList);

        $expectedPointList = '[{"latitude":53.5,"longitude":10.5},{"latitude":53.6,"longitude":10.6}]';

        $this->assertEquals($expectedPointList, $actualPointList);
    }

    public function testPositionListWithLatLngDateTimePositions(): void
    {
        $positionList = new PositionList();
        $positionList
            ->add((new Position(53.5, 10.5))->setDateTime(new \DateTime('2011-06-24 19:00:00', new \DateTimeZone('UTC'))))
            ->add((new Position(53.6, 10.6))->setDateTime(new \DateTime('2011-06-24 19:15:00', new \DateTimeZone('UTC'))));

        $converter = new PositionListToJsonConverter($this->createSerializer());

        $actualPointList = $converter->convert($positionList);

        $expectedPointList = '[{"latitude":53.5,"longitude":10.5,"date_time":1308942000},{"latitude":53.6,"longitude":10.6,"date_time":1308942900}]';

        $this->assertEquals($expectedPointList, $actualPointList);
    }

    protected function createSerializer(): SerializerInterface
    {
        return SerializerBuilder::create()
            ->setPropertyNamingStrategy(new CamelCaseNamingStrategy())
            ->build();
    }
}
