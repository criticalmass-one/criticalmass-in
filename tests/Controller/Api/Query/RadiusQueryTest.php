<?php declare(strict_types=1);

namespace Tests\Controller\Api\Query;

use App\Criticalmass\Geo\Coord\CoordInterface;
use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Ride;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Controller\Api\AbstractApiControllerTestCase;
use Tests\Coords;

class RadiusQueryTest extends AbstractApiControllerTestCase
{
    #[DataProvider('apiClassProvider')]
    public function testResultListForParameterizedDistance(string $fqcn, CoordInterface $centerCoord, float $radius, int $minExpected, int $maxExpected): void
    {
        $this->client->request('GET', sprintf('%s?centerLatitude=%f&centerLongitude=%f&radius=%f', $this->getApiEndpointForFqcn($fqcn), $centerCoord->getLatitude(), $centerCoord->getLongitude(), $radius));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $resultList = $this->getJsonResponse();

        $this->assertIsArray($resultList);
        $this->assertGreaterThanOrEqual($minExpected, count($resultList));
        $this->assertLessThanOrEqual($maxExpected, count($resultList));
    }

    public static function apiClassProvider(): array
    {
        // Fixtures have: Hamburg, Berlin, Munich, Kiel
        // Buedelsdorf is ~30km from Kiel, ~85km from Hamburg
        return [
            // Very small radius (10km) - should find nothing
            [City::class, Coords::buedelsdorf(), 10, 0, 0],
            // 100km radius - should find Kiel and Hamburg
            [City::class, Coords::buedelsdorf(), 100, 1, 4],
            // 500km radius - should find all 4 cities
            [City::class, Coords::buedelsdorf(), 500, 2, 4],
            // Small radius for rides
            [Ride::class, Coords::buedelsdorf(), 10, 0, 0],
            // Large radius should find rides
            [Ride::class, Coords::buedelsdorf(), 500, 1, 10],
            // Photos from Hamburg, Berlin, Munich
            [Photo::class, Coords::buedelsdorf(), 10, 0, 0],
            [Photo::class, Coords::buedelsdorf(), 500, 1, 10],
        ];
    }
}
