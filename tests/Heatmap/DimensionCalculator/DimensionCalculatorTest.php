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

    public function testOffsetLatLngsZoomLevel0(): void
    {
        $heatmapDimension = DimensionCalculator::calculate($this->createTestPathList(), 0);

        $this->assertEquals(189.920702, $heatmapDimension->getLeftOffset());
        $this->assertEquals(189.998042, $heatmapDimension->getRightOffset());
        $this->assertEquals(31.469759779807, $heatmapDimension->getTopOffset());
        $this->assertEquals(31.501612779807, $heatmapDimension->getBottomOffset());
    }

    public function testOffsetLatLngsZoomLevel1(): void
    {
        $heatmapDimension = DimensionCalculator::calculate($this->createTestPathList(), 1);

        $this->assertEquals(9.920702, $heatmapDimension->getLeftOffset());
        $this->assertEquals(9.998042, $heatmapDimension->getRightOffset());
        $this->assertEquals(31.469759779807, $heatmapDimension->getTopOffset());
        $this->assertEquals(31.501612779807, $heatmapDimension->getBottomOffset());
    }

    public function testOffsetLatLngsZoomLevel10(): void
    {
        $heatmapDimension = DimensionCalculator::calculate($this->createTestPathList(), 10);

        $this->assertEquals(0.076952, $heatmapDimension->getLeftOffset());
        $this->assertEquals(0.154292, $heatmapDimension->getRightOffset());
        $this->assertEquals(0.16734179689897, $heatmapDimension->getTopOffset());
        $this->assertEquals(0.19919479689898, $heatmapDimension->getBottomOffset());
    }

    public function testOffsetLatLngsZoomLevel15(): void
    {
        $heatmapDimension = DimensionCalculator::calculate($this->createTestPathList(), 15);

        $this->assertEquals(0.000047703125000353, $heatmapDimension->getLeftOffset());
        $this->assertEquals(0.00048340624999987, $heatmapDimension->getRightOffset());
        $this->assertEquals(0.004614654559802, $heatmapDimension->getTopOffset());
        $this->assertEquals(0.0038467855280757, $heatmapDimension->getBottomOffset());
    }

    public function testOffsetLatLngsZoomLevel19(): void
    {
        $heatmapDimension = DimensionCalculator::calculate($this->createTestPathList(), 19);

        $this->assertEquals(0.000047703125000353, $heatmapDimension->getLeftOffset());
        $this->assertEquals(0.00048340624999987, $heatmapDimension->getRightOffset());
        $this->assertEquals(0.00013077709895271, $heatmapDimension->getTopOffset());
        $this->assertEquals(0.00017536298834386, $heatmapDimension->getBottomOffset());
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