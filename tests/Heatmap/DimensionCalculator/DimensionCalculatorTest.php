<?php declare(strict_types=1);

namespace Tests\Heatmap\DimensionCalculator;

use App\Criticalmass\Heatmap\DimensionCalculator\DimensionCalculator;
use App\Criticalmass\Heatmap\Path\Path;
use App\Criticalmass\Heatmap\Path\PathList;
use Caldera\GeoBasic\Coord\Coord;
use PHPUnit\Framework\TestCase;

class DimensionCalculatorTest extends TestCase
{
    public function testMinMaxLatLngsZoomLevel1(): void
    {
        $heatmapDimension = DimensionCalculator::calculate($this->createTestPathList(), 1);

        $this->assertEquals(9.920702, $heatmapDimension->getLeftLongitude());
        $this->assertEquals(9.998042, $heatmapDimension->getRightLongitude());
        $this->assertEquals(53.581369, $heatmapDimension->getTopLatitude());
        $this->assertEquals(53.549516, $heatmapDimension->getBottomLatitude());
    }

    public function testMinMaxLatLngsZoomLevel10(): void
    {
        $heatmapDimension = DimensionCalculator::calculate($this->createTestPathList(), 10);

        $this->assertEquals(9.920702, $heatmapDimension->getLeftLongitude());
        $this->assertEquals(9.998042, $heatmapDimension->getRightLongitude());
        $this->assertEquals(53.581369, $heatmapDimension->getTopLatitude());
        $this->assertEquals(53.549516, $heatmapDimension->getBottomLatitude());
    }

    public function testMinMaxLatLngsZoomLevel15(): void
    {
        $heatmapDimension = DimensionCalculator::calculate($this->createTestPathList(), 15);

        $this->assertEquals(9.920702, $heatmapDimension->getLeftLongitude());
        $this->assertEquals(9.998042, $heatmapDimension->getRightLongitude());
        $this->assertEquals(53.581369, $heatmapDimension->getTopLatitude());
        $this->assertEquals(53.549516, $heatmapDimension->getBottomLatitude());
    }

    public function testMinMaxTilesZoomLevel0(): void
    {
        $heatmapDimension = DimensionCalculator::calculate($this->createTestPathList(), 0);

        $this->assertEquals(0, $heatmapDimension->getLeftTile());
        $this->assertEquals(0, $heatmapDimension->getRightTile());
        $this->assertEquals(0, $heatmapDimension->getTopTile());
        $this->assertEquals(0, $heatmapDimension->getBottomTile());
    }

    public function testMinMaxTilesZoomLevel1(): void
    {
        $heatmapDimension = DimensionCalculator::calculate($this->createTestPathList(), 1);

        $this->assertEquals(1, $heatmapDimension->getLeftTile());
        $this->assertEquals(1, $heatmapDimension->getRightTile());
        $this->assertEquals(0, $heatmapDimension->getTopTile());
        $this->assertEquals(0, $heatmapDimension->getBottomTile());
    }

    public function testMinMaxTilesZoomLevel10(): void
    {
        $heatmapDimension = DimensionCalculator::calculate($this->createTestPathList(), 10);

        $this->assertEquals(540, $heatmapDimension->getLeftTile());
        $this->assertEquals(540, $heatmapDimension->getRightTile());
        $this->assertEquals(330, $heatmapDimension->getTopTile());
        $this->assertEquals(330, $heatmapDimension->getBottomTile());
    }

    public function testMinMaxTilesZoomLevel12(): void
    {
        $heatmapDimension = DimensionCalculator::calculate($this->createTestPathList(), 12);

        $this->assertEquals(2160, $heatmapDimension->getLeftTile());
        $this->assertEquals(2161, $heatmapDimension->getRightTile());
        $this->assertEquals(1323, $heatmapDimension->getTopTile());
        $this->assertEquals(1323, $heatmapDimension->getBottomTile());
    }

    public function testMinMaxTilesZoomLevel15(): void
    {
        $heatmapDimension = DimensionCalculator::calculate($this->createTestPathList(), 15);

        $this->assertEquals(17287, $heatmapDimension->getLeftTile());
        $this->assertEquals(17294, $heatmapDimension->getRightTile());
        $this->assertEquals(10585, $heatmapDimension->getTopTile());
        $this->assertEquals(10590, $heatmapDimension->getBottomTile());
    }

    public function testMinMaxTilesZoomLevel19(): void
    {
        $heatmapDimension = DimensionCalculator::calculate($this->createTestPathList(), 19);

        $this->assertEquals(276592, $heatmapDimension->getLeftTile());
        $this->assertEquals(276704, $heatmapDimension->getRightTile());
        $this->assertEquals(169371, $heatmapDimension->getTopTile());
        $this->assertEquals(169449, $heatmapDimension->getBottomTile());
    }

    protected function createTestPathList(): PathList
    {
        $icans = new Coord(53.554971, 9.986380);
        $parship = new Coord(53.550048, 9.998042);
        $jimdo = new Coord(53.565295, 9.920702);
        $healthcareUnited = new Coord(53.581369, 9.977529);
        $bornholdtLee = new Coord(53.549516, 9.979791);

        $pathList = new PathList();
        $pathList
            ->add(new Path($icans, $parship))
            ->add(new Path($parship, $jimdo))
            ->add(new Path($jimdo, $healthcareUnited))
            ->add(new Path($healthcareUnited, $bornholdtLee))
            ->add(new Path($bornholdtLee, $parship));

        return $pathList;
    }
}