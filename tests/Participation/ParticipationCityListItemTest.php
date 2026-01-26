<?php declare(strict_types=1);

namespace Tests\Participation;

use App\Criticalmass\Participation\CityList\ParticipationCityListItem;
use App\Entity\City;
use PHPUnit\Framework\TestCase;

class ParticipationCityListItemTest extends TestCase
{
    private function createCity(int $id): City
    {
        $city = $this->createMock(City::class);
        $city->method('getId')->willReturn($id);

        return $city;
    }

    public function testConstructorWithDefaultCounter(): void
    {
        $city = $this->createCity(1);
        $item = new ParticipationCityListItem($city);

        $this->assertSame($city, $item->getCity());
        $this->assertEquals(1, $item->getCounter());
    }

    public function testConstructorWithCustomCounter(): void
    {
        $city = $this->createCity(1);
        $item = new ParticipationCityListItem($city, 5);

        $this->assertEquals(5, $item->getCounter());
    }

    public function testIncCounter(): void
    {
        $city = $this->createCity(1);
        $item = new ParticipationCityListItem($city);

        $item->incCounter();

        $this->assertEquals(2, $item->getCounter());
    }

    public function testIncCounterMultipleTimes(): void
    {
        $city = $this->createCity(1);
        $item = new ParticipationCityListItem($city);

        $item->incCounter();
        $item->incCounter();
        $item->incCounter();

        $this->assertEquals(4, $item->getCounter());
    }

    public function testIncCounterWithSteps(): void
    {
        $city = $this->createCity(1);
        $item = new ParticipationCityListItem($city);

        $item->incCounter(5);

        $this->assertEquals(6, $item->getCounter());
    }

    public function testIncCounterReturnsSelf(): void
    {
        $city = $this->createCity(1);
        $item = new ParticipationCityListItem($city);

        $result = $item->incCounter();

        $this->assertSame($item, $result);
    }

    public function testGetCity(): void
    {
        $city = $this->createCity(42);
        $item = new ParticipationCityListItem($city);

        $this->assertSame($city, $item->getCity());
    }

    public function testConstructorWithZeroCounter(): void
    {
        $city = $this->createCity(1);
        $item = new ParticipationCityListItem($city, 0);

        $this->assertEquals(0, $item->getCounter());
    }
}
