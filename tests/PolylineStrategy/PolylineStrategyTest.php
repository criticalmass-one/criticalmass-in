<?php declare(strict_types=1);

namespace Tests\PolylineStrategy;

use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\PolylineGenerator\PolylineGenerator;
use App\Criticalmass\Geo\PolylineGenerator\PolylineStrategy\FullPolylineStrategy;
use App\Criticalmass\Geo\PositionList\PositionList;
use PHPUnit\Framework\TestCase;

class PolylineStrategyTest extends TestCase
{
    public function testPolylineGenerator(): void
    {
        $positionList = new PositionList();

        $positionList
            ->push(new Position(57, 10))
            ->push(new Position(57, 9))
            ->push(new Position(56, 9))
            ->push(new Position(56, 10));

        $polylineGenerator = new PolylineGenerator();
        $polyline = $polylineGenerator
            ->setStrategy(new FullPolylineStrategy())
            ->execute($positionList);

        $this->assertEquals('_y{zI_c`|@?~hbE~hbE??_ibE', $polyline);
    }

    public function testPolylineGeneratorWithEmptyPositionList(): void
    {
        $positionList = new PositionList();

        $polylineGenerator = new PolylineGenerator();
        $polyline = $polylineGenerator
            ->setStrategy(new FullPolylineStrategy())
            ->execute($positionList);

        $this->assertEquals('', $polyline);
    }
}
