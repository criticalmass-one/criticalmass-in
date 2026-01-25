<?php declare(strict_types=1);

namespace Tests\Geo\GeoUtil;

use App\Criticalmass\Geo\Coord\Coord;
use App\Criticalmass\Geo\GeoUtil\GeoUtil;
use PHPUnit\Framework\TestCase;

class GeoUtilTest extends TestCase
{
    // ============================================
    // calculateDistance Tests (with CoordInterface)
    // ============================================

    public function testCalculateDistanceSamePoint(): void
    {
        $coord1 = new Coord(52.520008, 13.404954); // Berlin
        $coord2 = new Coord(52.520008, 13.404954); // Berlin

        $distance = GeoUtil::calculateDistance($coord1, $coord2);

        $this->assertEquals(0.0, $distance);
    }

    public function testCalculateDistanceBerlinHamburg(): void
    {
        $berlin = new Coord(52.520008, 13.404954);
        $hamburg = new Coord(53.551086, 9.993682);

        $distance = GeoUtil::calculateDistance($berlin, $hamburg);

        // Berlin to Hamburg is approximately 255-290 km
        $this->assertGreaterThan(200, $distance);
        $this->assertLessThan(350, $distance);
    }

    public function testCalculateDistanceShortDistance(): void
    {
        // Two points about 1km apart in Braunschweig
        $point1 = new Coord(52.2681, 10.5211);
        $point2 = new Coord(52.2770, 10.5211);

        $distance = GeoUtil::calculateDistance($point1, $point2);

        // Should be approximately 1 km
        $this->assertGreaterThan(0.5, $distance);
        $this->assertLessThan(2.0, $distance);
    }

    public function testCalculateDistanceIsSymmetric(): void
    {
        $coord1 = new Coord(52.520008, 13.404954);
        $coord2 = new Coord(53.551086, 9.993682);

        $distance1 = GeoUtil::calculateDistance($coord1, $coord2);
        $distance2 = GeoUtil::calculateDistance($coord2, $coord1);

        $this->assertEquals($distance1, $distance2);
    }

    public function testCalculateDistanceReturnsPositiveValue(): void
    {
        $coord1 = new Coord(52.520008, 13.404954);
        $coord2 = new Coord(48.137154, 11.576124); // Munich

        $distance = GeoUtil::calculateDistance($coord1, $coord2);

        $this->assertGreaterThan(0, $distance);
    }

    // ============================================
    // calculateDistanceFromCoords Tests
    // ============================================

    public function testCalculateDistanceFromCoordsSamePoint(): void
    {
        $distance = GeoUtil::calculateDistanceFromCoords(
            52.520008, 13.404954,
            52.520008, 13.404954
        );

        $this->assertEquals(0.0, $distance);
    }

    public function testCalculateDistanceFromCoordsBerlinHamburg(): void
    {
        $distance = GeoUtil::calculateDistanceFromCoords(
            52.520008, 13.404954, // Berlin
            53.551086, 9.993682  // Hamburg
        );

        // Berlin to Hamburg is approximately 255-290 km
        $this->assertGreaterThan(200, $distance);
        $this->assertLessThan(350, $distance);
    }

    public function testCalculateDistanceFromCoordsMatchesObjectMethod(): void
    {
        $lat1 = 52.520008;
        $lon1 = 13.404954;
        $lat2 = 53.551086;
        $lon2 = 9.993682;

        $coord1 = new Coord($lat1, $lon1);
        $coord2 = new Coord($lat2, $lon2);

        $distanceFromCoords = GeoUtil::calculateDistanceFromCoords($lat1, $lon1, $lat2, $lon2);
        $distanceFromObjects = GeoUtil::calculateDistance($coord1, $coord2);

        $this->assertEquals($distanceFromCoords, $distanceFromObjects);
    }

    // ============================================
    // Edge Cases
    // ============================================

    public function testCalculateDistanceWithNegativeCoordinates(): void
    {
        // New York (negative longitude)
        $newYork = new Coord(40.712776, -74.005974);
        // London
        $london = new Coord(51.507351, -0.127758);

        $distance = GeoUtil::calculateDistance($newYork, $london);

        // Should return a positive distance
        $this->assertGreaterThan(0, $distance);
    }

    public function testCalculateDistanceWithSouthernHemisphere(): void
    {
        // Sydney (southern hemisphere)
        $sydney = new Coord(-33.868820, 151.209290);
        // Melbourne
        $melbourne = new Coord(-37.813628, 144.963058);

        $distance = GeoUtil::calculateDistance($sydney, $melbourne);

        // Should return a positive distance
        $this->assertGreaterThan(0, $distance);
    }

    public function testCalculateDistanceWithZeroCoordinates(): void
    {
        $origin = new Coord(0.0, 0.0);
        $point = new Coord(1.0, 1.0);

        $distance = GeoUtil::calculateDistance($origin, $point);

        // Distance from (0,0) to (1,1) should be roughly 157 km
        // (1 degree latitude ≈ 111km, 1 degree longitude at equator ≈ 111km)
        $this->assertGreaterThan(100, $distance);
        $this->assertLessThan(200, $distance);
    }

    // ============================================
    // Accuracy Tests
    // ============================================

    public function testCalculateDistanceApproximatelyCorrectForGermanCities(): void
    {
        // These are simplified calculations, so we test within reasonable ranges
        $cities = [
            'Berlin-Munich' => [
                'from' => new Coord(52.520008, 13.404954),
                'to' => new Coord(48.137154, 11.576124),
                'min' => 400,
                'max' => 600,
            ],
            'Hamburg-Frankfurt' => [
                'from' => new Coord(53.551086, 9.993682),
                'to' => new Coord(50.110924, 8.682127),
                'min' => 350,
                'max' => 500,
            ],
            'Cologne-Dusseldorf' => [
                'from' => new Coord(50.937531, 6.960279),
                'to' => new Coord(51.227741, 6.773456),
                'min' => 20,
                'max' => 60,
            ],
        ];

        foreach ($cities as $route => $data) {
            $distance = GeoUtil::calculateDistance($data['from'], $data['to']);

            $this->assertGreaterThan(
                $data['min'],
                $distance,
                sprintf('%s distance should be greater than %d km', $route, $data['min'])
            );
            $this->assertLessThan(
                $data['max'],
                $distance,
                sprintf('%s distance should be less than %d km', $route, $data['max'])
            );
        }
    }

    public function testVerySmallDistanceIsDetected(): void
    {
        // Two points 10 meters apart (approximately)
        $point1 = new Coord(52.520000, 13.404954);
        $point2 = new Coord(52.520090, 13.404954); // ~10m north

        $distance = GeoUtil::calculateDistance($point1, $point2);

        // Should be approximately 0.01 km = 10 meters
        $this->assertGreaterThan(0.005, $distance);
        $this->assertLessThan(0.02, $distance);
    }
}
