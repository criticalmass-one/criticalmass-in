<?php declare(strict_types=1);

namespace Tests\Participation;

use App\Criticalmass\Participation\CityList\ParticipationCityList;
use App\Criticalmass\Participation\CityList\ParticipationCityListItem;
use App\Entity\City;
use PHPUnit\Framework\TestCase;

class ParticipationCityListTest extends TestCase
{
    private function createCityItem(int $cityId): ParticipationCityListItem
    {
        $city = $this->createMock(City::class);
        $city->method('getId')->willReturn($cityId);

        return new ParticipationCityListItem($city);
    }

    public function testEmptyListCount(): void
    {
        $list = new ParticipationCityList();

        $this->assertCount(0, $list);
    }

    public function testAddCityItem(): void
    {
        $list = new ParticipationCityList();
        $item = $this->createCityItem(1);

        $result = $list->addCityItem($item);

        $this->assertSame($list, $result);
        $this->assertCount(1, $list);
    }

    public function testAddMultipleDifferentCities(): void
    {
        $list = new ParticipationCityList();

        $list->addCityItem($this->createCityItem(1));
        $list->addCityItem($this->createCityItem(2));
        $list->addCityItem($this->createCityItem(3));

        $this->assertCount(3, $list);
    }

    public function testAddSameCityIncrementsCounter(): void
    {
        $list = new ParticipationCityList();

        $list->addCityItem($this->createCityItem(1));
        $list->addCityItem($this->createCityItem(1));

        $this->assertCount(1, $list);

        $items = $list->getList();
        $this->assertEquals(2, $items[1]->getCounter());
    }

    public function testAddSameCityMultipleTimes(): void
    {
        $list = new ParticipationCityList();

        $list->addCityItem($this->createCityItem(1));
        $list->addCityItem($this->createCityItem(1));
        $list->addCityItem($this->createCityItem(1));

        $this->assertCount(1, $list);

        $items = $list->getList();
        $this->assertEquals(3, $items[1]->getCounter());
    }

    public function testGetListReturnsArray(): void
    {
        $list = new ParticipationCityList();

        $this->assertIsArray($list->getList());
        $this->assertEmpty($list->getList());
    }

    public function testSetList(): void
    {
        $list = new ParticipationCityList();
        $items = [
            1 => $this->createCityItem(1),
            2 => $this->createCityItem(2),
        ];

        $result = $list->setList($items);

        $this->assertSame($list, $result);
        $this->assertCount(2, $list);
        $this->assertEquals($items, $list->getList());
    }

    public function testCountableInterface(): void
    {
        $list = new ParticipationCityList();

        $this->assertInstanceOf(\Countable::class, $list);
    }

    public function testMixedCityAdditions(): void
    {
        $list = new ParticipationCityList();

        $list->addCityItem($this->createCityItem(1));
        $list->addCityItem($this->createCityItem(2));
        $list->addCityItem($this->createCityItem(1));
        $list->addCityItem($this->createCityItem(3));
        $list->addCityItem($this->createCityItem(2));

        $this->assertCount(3, $list);

        $items = $list->getList();
        $this->assertEquals(2, $items[1]->getCounter());
        $this->assertEquals(2, $items[2]->getCounter());
        $this->assertEquals(1, $items[3]->getCounter());
    }
}
