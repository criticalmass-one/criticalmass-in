<?php declare(strict_types=1);

namespace Tests\Heatmap\CoordCalculator;

use App\Criticalmass\Heatmap\CoordCalculator\CoordCalculator;
use Caldera\GeoBasic\Coord\Coord;
use PHPUnit\Framework\TestCase;

class CoordCalculatorTest extends TestCase
{
    public function testAudimaxCoordsAreConvertedToTileNumbersInZoomLevel10(): void
    {
        $this->assertEquals(330, CoordCalculator::latitudeToYTile(53.566515, 10));
        $this->assertEquals(540, CoordCalculator::longitudeToXTile(9.984829, 10));
    }

    public function testAudimaxCoordsAreConvertedToTileNumbersInZoomLevel15(): void
    {
        $this->assertEquals(10587, CoordCalculator::latitudeToYTile(53.566515, 15));
        $this->assertEquals(17292, CoordCalculator::longitudeToXTile(9.984829, 15));
    }

    public function testAudimaxTileNumbersAreConvertedToCoordsInZoomLevel10(): void
    {
        $this->assertEquals(53.74871079689899, CoordCalculator::yTileToLatitude(330, 10));
        $this->assertEquals(9.84375, CoordCalculator::xTileToLongitude(540, 10));
    }

    public function testAudimaxTileNumbersAreConvertedToCoordsInZoomLevel15(): void
    {
        $this->assertEquals(53.572938326486096, CoordCalculator::yTileToLatitude(10587, 15));
        $this->assertEquals(9.9755859375, CoordCalculator::xTileToLongitude(17292, 15));
    }
}