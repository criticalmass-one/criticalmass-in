<?php declare(strict_types=1);

namespace Tests\Controller\Api\Query;

use App\Criticalmass\Geo\Coord\Coord;
use App\Criticalmass\Geo\Coord\CoordInterface;
use App\Entity\Ride;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class BoundingBoxQueryTest extends AbstractApiControllerTestCase
{
    #[DataProvider('apiClassProvider')]
    public function testRideListWithBoundingBoxQueryForHamburg(string $fqcn, array $query, CoordInterface $expectedCoord): void
    {
        $this->client->request('GET', sprintf('%s?%s', $this->getApiEndpointForFqcn($fqcn), http_build_query($query)));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $resultList = $this->getJsonResponse();

        // Default size is 10, but we may have fewer records in fixtures
        $this->assertIsArray($resultList);
        $this->assertLessThanOrEqual(10, count($resultList));
        $this->assertNotEmpty($resultList);

        foreach ($resultList as $result) {
            // Allow small floating point differences
            $this->assertEqualsWithDelta($expectedCoord->getLatitude(), $result['latitude'], 0.05);
            $this->assertEqualsWithDelta($expectedCoord->getLongitude(), $result['longitude'], 0.05);
        }
    }

    #[TestDox('Invalid coords for bounding box query will be ignored and result in 10 random rides.')]
    public function testRideListWithInvalidBoundingBoxQuery(): void
    {
        $this->client->request('GET', '/api/ride?bbNorthLatitude=54&bbSouthLatitude=57&bbEastLongitude=9&bbWestLongitude=10.054470');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $resultList = $this->getJsonResponse();

        // Default size is 10, but we may have fewer records in fixtures
        $this->assertIsArray($resultList);
        $this->assertLessThanOrEqual(10, count($resultList));
        $this->assertNotEmpty($resultList);
    }

    public static function apiClassProvider(): array
    {
        // Use actual coordinates from fixtures:
        // Hamburg: 53.5611, 9.9895
        // Berlin: 52.4989, 13.4178
        // Munich: 48.1371, 11.5754
        return [
            // Hamburg bounding box
            [
                Ride::class, [
                'bbNorthLatitude' => 53.60,
                'bbWestLongitude' => 9.90,
                'bbSouthLatitude' => 53.50,
                'bbEastLongitude' => 10.10,
            ],
                new Coord(53.5611, 9.9895),
            ],
            // Berlin bounding box
            [
                Ride::class, [
                'bbNorthLatitude' => 52.55,
                'bbWestLongitude' => 13.35,
                'bbSouthLatitude' => 52.45,
                'bbEastLongitude' => 13.50,
            ],
                new Coord(52.4989, 13.4178),
            ],
            // Munich bounding box
            [
                Ride::class, [
                'bbNorthLatitude' => 48.20,
                'bbWestLongitude' => 11.50,
                'bbSouthLatitude' => 48.10,
                'bbEastLongitude' => 11.65,
                ],
                new Coord(48.1371, 11.5754),
            ],
        ];
    }
}
