<?php declare(strict_types=1);

namespace Tests\Geo\Coord;

use App\Criticalmass\Geo\Coord\Coord;
use PHPUnit\Framework\TestCase;

class CoordTest extends TestCase
{
    public function testConstructorWithValues(): void
    {
        $coord = new Coord(53.5511, 9.9937);

        $this->assertEquals(53.5511, $coord->getLatitude());
        $this->assertEquals(9.9937, $coord->getLongitude());
    }

    public function testConstructorWithDefaults(): void
    {
        $coord = new Coord();

        $this->assertNull($coord->getLatitude());
        $this->assertNull($coord->getLongitude());
    }

    public function testConstructorWithNulls(): void
    {
        $coord = new Coord(null, null);

        $this->assertNull($coord->getLatitude());
        $this->assertNull($coord->getLongitude());
    }

    public function testConstructorWithOnlyLatitude(): void
    {
        $coord = new Coord(53.5511);

        $this->assertEquals(53.5511, $coord->getLatitude());
        $this->assertNull($coord->getLongitude());
    }

    public function testNegativeCoordinates(): void
    {
        $coord = new Coord(-33.8688, -151.2093);

        $this->assertEquals(-33.8688, $coord->getLatitude());
        $this->assertEquals(-151.2093, $coord->getLongitude());
    }

    public function testZeroCoordinates(): void
    {
        $coord = new Coord(0.0, 0.0);

        $this->assertEquals(0.0, $coord->getLatitude());
        $this->assertEquals(0.0, $coord->getLongitude());
    }

    public function testPrecision(): void
    {
        $coord = new Coord(53.551085, 9.993682);

        $this->assertEqualsWithDelta(53.551085, $coord->getLatitude(), 0.000001);
        $this->assertEqualsWithDelta(9.993682, $coord->getLongitude(), 0.000001);
    }
}
