<?php declare(strict_types=1);

namespace Tests\PolylineStrategy;

use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\PolylineGenerator\PolylineGenerator;
use App\Criticalmass\Geo\PolylineGenerator\PolylineStrategy\FullPolylineStrategy;
use App\Criticalmass\Geo\PolylineGenerator\PolylineStrategy\ReducedPolylineStrategy;
use App\Criticalmass\Geo\PositionList\PositionList;
use PHPUnit\Framework\TestCase;

class PolylineStrategyTest extends TestCase
{
    protected function createTestPositionList(): PositionList
    {
        $positionList = new PositionList();

        $longitude = 10;

        for ($latitude = 10; $latitude < 50; ++$latitude) {
            $positionList->add(new Position($latitude, $longitude));
        }

        $latitude = 50;

        for ($longitude = 10; $longitude < 50; ++$longitude) {
            $positionList->add(new Position($latitude, $longitude));
        }

        return $positionList;
    }

    public function testPolylineGenerator(): void
    {
        $polylineGenerator = new PolylineGenerator();
        $polyline = $polylineGenerator
            ->setStrategy(new FullPolylineStrategy())
            ->execute($this->createTestPositionList());

        $this->assertEquals('_c`|@_c`|@_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE??_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE', $polyline);
    }

    public function testPolylineGeneratorWithReducedStrategy(): void
    {
        $polylineGenerator = new PolylineGenerator();
        $polyline = $polylineGenerator
            ->setStrategy(new ReducedPolylineStrategy())
            ->execute($this->createTestPositionList());

        $this->assertEquals('_c`|@_c`|@_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE??_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE?_ibE', $polyline);
    }

    public function testPolylineGeneratorWithEmptyPositionList(): void
    {
        $polylineGenerator = new PolylineGenerator();
        $polyline = $polylineGenerator
            ->setStrategy(new FullPolylineStrategy())
            ->execute(new PositionList());

        $this->assertEquals('', $polyline);
    }
}
