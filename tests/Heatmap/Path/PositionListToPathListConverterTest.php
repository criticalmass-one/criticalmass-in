<?php declare(strict_types=1);

namespace Tests\Heatmap\Path;

use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\PositionList\PositionList;
use App\Criticalmass\Heatmap\Path\Path;
use App\Criticalmass\Heatmap\Path\PathList;
use App\Criticalmass\Heatmap\Path\PositionListToPathListConverter;
use Caldera\GeoBasic\Coord\Coord;
use PHPUnit\Framework\TestCase;

class PositionListToPathListConverterTest extends TestCase
{
    public function testConverterWithEmptyList(): void
    {
        $expectedPathList = new PathList();

        $positionList = new PositionList();

        $actualPathList = PositionListToPathListConverter::convert($positionList);

        $this->assertEquals($expectedPathList, $actualPathList);
    }

    public function testConverterWithFourPositions(): void
    {
        $expectedPathList = new PathList();
        $expectedPathList->add(new Path(new Coord(57.1, 10.0), new Coord(57.2, 9.9)));
        $expectedPathList->add(new Path(new Coord(57.2, 9.9), new Coord(57.3, 9.7)));
        $expectedPathList->add(new Path(new Coord(57.3, 9.7), new Coord(57.4, 9.4)));

        $positionList = new PositionList();

        $positionList
            ->push(new Position(57.1, 10.0))
            ->push(new Position(57.2, 9.9))
            ->push(new Position(57.3, 9.7))
            ->push(new Position(57.4, 9.4));

        $actualPathList = PositionListToPathListConverter::convert($positionList);

        $this->assertEquals($expectedPathList, $actualPathList);
    }
}