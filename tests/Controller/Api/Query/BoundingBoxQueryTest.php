<?php declare(strict_types=1);

namespace Tests\Controller\Api\Query;

use App\Criticalmass\Geo\Coord\Coord;
use App\Criticalmass\Geo\Coord\CoordInterface;
use App\Entity\Ride;
use App\EntityInterface\CoordinateInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTest;

class BoundingBoxQueryTest extends AbstractApiControllerTest
{
    #[DataProvider('apiClassProvider')]
    public function testRideListWithBoundingBoxQueryForHamburg(string $fqcn, array $query, CoordInterface $expectedCoord): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?%s', $this->getApiEndpointForFqcn($fqcn), http_build_query($query)));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        // Default size is 10, but we may have fewer records in fixtures
        $this->assertLessThanOrEqual(10, count($resultList));
        $this->assertNotEmpty($resultList);

        /** @var CoordinateInterface $result */
        foreach ($resultList as $result) {
            // Allow small floating point differences
            $this->assertEqualsWithDelta($expectedCoord->getLatitude(), $result->getLatitude(), 0.05);
            $this->assertEqualsWithDelta($expectedCoord->getLongitude(), $result->getLongitude(), 0.05);
        }
    }

    #[TestDox('Invalid coords for bounding box query will be ignored and result in 10 random rides.')]
    public function testRideListWithInvalidBoundingBoxQuery(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?bbNorthLatitude=54&bbSouthLatitude=57&bbEastLongitude=9&bbWestLongitude=10.054470');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        // Default size is 10, but we may have fewer records in fixtures
        $this->assertLessThanOrEqual(10, count($actualRideList));
        $this->assertNotEmpty($actualRideList);
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
