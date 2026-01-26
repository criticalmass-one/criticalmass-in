<?php declare(strict_types=1);

namespace Tests\Geo\Coordinate;

use App\Criticalmass\Geo\Coord\Coord;
use App\Criticalmass\Geo\Coordinate\Coordinate;
use PHPUnit\Framework\TestCase;

class CoordinateTest extends TestCase
{
    public function testToArray(): void
    {
        $coordinate = new Coordinate(53.5511, 9.9937);

        $this->assertEquals([53.5511, 9.9937], $coordinate->toArray());
    }

    public function testToInversedArray(): void
    {
        $coordinate = new Coordinate(53.5511, 9.9937);

        $this->assertEquals([9.9937, 53.5511], $coordinate->toInversedArray());
    }

    public function testToLatLngArray(): void
    {
        $coordinate = new Coordinate(53.5511, 9.9937);

        $expected = ['lat' => 53.5511, 'lng' => 9.9937];
        $this->assertEquals($expected, $coordinate->toLatLngArray());
    }

    public function testToLatLonArray(): void
    {
        $coordinate = new Coordinate(53.5511, 9.9937);

        $expected = ['lat' => 53.5511, 'lon' => 9.9937];
        $this->assertEquals($expected, $coordinate->toLatLonArray());
    }

    public function testNorthOf(): void
    {
        $hamburg = new Coordinate(53.5511, 9.9937);
        $munich = new Coord(48.1351, 11.5820);

        $this->assertTrue($hamburg->northOf($munich));
        $this->assertFalse($hamburg->southOf($munich));
    }

    public function testSouthOf(): void
    {
        $munich = new Coordinate(48.1351, 11.5820);
        $hamburg = new Coord(53.5511, 9.9937);

        $this->assertTrue($munich->southOf($hamburg));
        $this->assertFalse($munich->northOf($hamburg));
    }

    public function testWestOf(): void
    {
        $hamburg = new Coordinate(53.5511, 9.9937);
        $berlin = new Coord(52.5200, 13.4050);

        $this->assertTrue($hamburg->westOf($berlin));
        $this->assertFalse($hamburg->eastOf($berlin));
    }

    public function testEastOf(): void
    {
        $berlin = new Coordinate(52.5200, 13.4050);
        $hamburg = new Coord(53.5511, 9.9937);

        $this->assertTrue($berlin->eastOf($hamburg));
        $this->assertFalse($berlin->westOf($hamburg));
    }

    public function testSameLatitudeNotNorthOrSouth(): void
    {
        $a = new Coordinate(53.5511, 9.9937);
        $b = new Coord(53.5511, 13.4050);

        $this->assertFalse($a->northOf($b));
        $this->assertFalse($a->southOf($b));
    }

    public function testSameLongitudeNotEastOrWest(): void
    {
        $a = new Coordinate(53.5511, 9.9937);
        $b = new Coord(48.1351, 9.9937);

        $this->assertFalse($a->eastOf($b));
        $this->assertFalse($a->westOf($b));
    }

    public function testToArrayWithNullValues(): void
    {
        $coordinate = new Coordinate();

        $this->assertEquals([null, null], $coordinate->toArray());
    }

    public function testToLatLngArrayWithNullValues(): void
    {
        $coordinate = new Coordinate();

        $expected = ['lat' => null, 'lng' => null];
        $this->assertEquals($expected, $coordinate->toLatLngArray());
    }

    public function testInheritsFromCoord(): void
    {
        $coordinate = new Coordinate(52.5200, 13.4050);

        $this->assertInstanceOf(Coord::class, $coordinate);
        $this->assertEquals(52.5200, $coordinate->getLatitude());
        $this->assertEquals(13.4050, $coordinate->getLongitude());
    }
}
