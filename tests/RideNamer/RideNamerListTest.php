<?php declare(strict_types=1);

namespace Tests\RideNamer;

use App\Criticalmass\RideNamer\GermanCityDateRideNamer;
use App\Criticalmass\RideNamer\IsoCityDateRideNamer;
use App\Criticalmass\RideNamer\RideNamerInterface;
use App\Criticalmass\RideNamer\RideNamerList;
use PHPUnit\Framework\TestCase;

class RideNamerListTest extends TestCase
{
    public function testAddRideNamer(): void
    {
        $list = new RideNamerList();
        $namer = new IsoCityDateRideNamer();

        $result = $list->addRideNamer($namer);

        $this->assertSame($list, $result);
    }

    public function testGetList(): void
    {
        $list = new RideNamerList();
        $isoNamer = new IsoCityDateRideNamer();
        $germanNamer = new GermanCityDateRideNamer();

        $list->addRideNamer($isoNamer);
        $list->addRideNamer($germanNamer);

        $result = $list->getList();

        $this->assertCount(2, $result);
        $this->assertSame($isoNamer, $result[IsoCityDateRideNamer::class]);
        $this->assertSame($germanNamer, $result[GermanCityDateRideNamer::class]);
    }

    public function testGetRideNamerByFqcn(): void
    {
        $list = new RideNamerList();
        $isoNamer = new IsoCityDateRideNamer();
        $list->addRideNamer($isoNamer);

        $result = $list->getRideNamerByFqcn(IsoCityDateRideNamer::class);

        $this->assertSame($isoNamer, $result);
    }

    public function testGetRideNamerByFqcnReturnsNullForUnknown(): void
    {
        $list = new RideNamerList();
        $isoNamer = new IsoCityDateRideNamer();
        $list->addRideNamer($isoNamer);

        $result = $list->getRideNamerByFqcn('App\\NonExistent\\Namer');

        $this->assertNull($result);
    }

    public function testAddSameNamerTwiceOverwrites(): void
    {
        $list = new RideNamerList();
        $namer1 = new IsoCityDateRideNamer();
        $namer2 = new IsoCityDateRideNamer();

        $list->addRideNamer($namer1);
        $list->addRideNamer($namer2);

        $result = $list->getList();

        $this->assertCount(1, $result);
        $this->assertSame($namer2, $result[IsoCityDateRideNamer::class]);
    }

    public function testGetRideNamerByFqcnOnEmptyListThrowsError(): void
    {
        $list = new RideNamerList();

        $this->expectException(\TypeError::class);

        $list->getRideNamerByFqcn(IsoCityDateRideNamer::class);
    }
}
