<?php declare(strict_types=1);

namespace Tests\Heatmap\DimensionCalculator;

use App\Criticalmass\Heatmap\DimensionCalculator\HeatmapDimension;
use PHPUnit\Framework\TestCase;

class HeatmapDimensionTest extends TestCase
{
    public function testHeight(): void
    {
        $heatmapDimension = new HeatmapDimension(15);
        $heatmapDimension->setTopTile(10)
            ->setBottomTile(12);

        $this->assertEquals(3, $heatmapDimension->getHeight());
    }

    public function testWidth(): void
    {
        $heatmapDimension = new HeatmapDimension(15);
        $heatmapDimension->setLeftTile(21)
            ->setRightTile(25);

        $this->assertEquals(5, $heatmapDimension->getWidth());
    }

    public function testZoomLevel(): void
    {
        $heatmapDimension = new HeatmapDimension(15);

        $this->assertEquals(15, $heatmapDimension->getZoomLevel());
    }

    public function testSetZoomLevel(): void
    {
        $heatmapDimension = new HeatmapDimension(15);
        $heatmapDimension->setZoomLevel(13);

        $this->assertEquals(13, $heatmapDimension->getZoomLevel());
    }

    public function testLeftTile(): void
    {
        $heatmapDimension = new HeatmapDimension(15);
        $heatmapDimension->setLeftTile(3);

        $this->assertEquals(3, $heatmapDimension->getLeftTile());
    }

    public function testRightTile(): void
    {
        $heatmapDimension = new HeatmapDimension(15);
        $heatmapDimension->setRightTile(5);

        $this->assertEquals(5, $heatmapDimension->getRightTile());
    }

    public function testTopLatitude(): void
    {
        $heatmapDimension = new HeatmapDimension(15);
        $heatmapDimension->setTopLatitude(55.121);

        $this->assertEquals(55.121, $heatmapDimension->getTopLatitude());
    }

    public function testBottomTile(): void
    {
        $heatmapDimension = new HeatmapDimension(15);
        $heatmapDimension->setBottomTile(912);

        $this->assertEquals(912, $heatmapDimension->getBottomTile());
    }

    public function testBottomLatitude(): void
    {
        $heatmapDimension = new HeatmapDimension(15);
        $heatmapDimension->setBottomLatitude(57.112);

        $this->assertEquals(57.112, $heatmapDimension->getBottomLatitude());
    }

    public function testLeftLongitude(): void
    {
        $heatmapDimension = new HeatmapDimension(15);
        $heatmapDimension->setLeftLongitude(9.131);

        $this->assertEquals(9.131, $heatmapDimension->getLeftLongitude());
    }

    public function testTopTile(): void
    {
        $heatmapDimension = new HeatmapDimension(15);
        $heatmapDimension->setTopTile(351);

        $this->assertEquals(351, $heatmapDimension->getTopTile());
    }

    public function testRightLongitude(): void
    {
        $heatmapDimension = new HeatmapDimension(15);
        $heatmapDimension->setRightLongitude(10.5);

        $this->assertEquals(10.5, $heatmapDimension->getRightLongitude());
    }
}
