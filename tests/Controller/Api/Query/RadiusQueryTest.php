<?php declare(strict_types=1);

namespace Tests\Controller\Api\Query;

use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Ride;
use Caldera\GeoBasic\Coord\CoordInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Controller\Api\AbstractApiControllerTest;
use Tests\Coords;

class RadiusQueryTest extends AbstractApiControllerTest
{
    #[DataProvider('apiClassProvider')]
    public function testResultListForParameterizedDistance(string $fqcn, CoordInterface $centerCoord, float $radius, int $expectedResults): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?centerLatitude=%f&centerLongitude=%f&radius=%f', $this->getApiEndpointForFqcn($fqcn), $centerCoord->getLatitude(), $centerCoord->getLongitude(), $radius));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertCount($expectedResults, $resultList);
    }

    public static function apiClassProvider(): array
    {
        return [
            [City::class, Coords::buedelsdorf(), 10, 0],
            [City::class, Coords::buedelsdorf(), 100, 2],
            [City::class, Coords::buedelsdorf(), 250, 5],
            [Ride::class, Coords::buedelsdorf(), 10, 0],
            [Ride::class, Coords::buedelsdorf(), 100, 10],
            [Photo::class, Coords::buedelsdorf(), 10, 0],
            [Photo::class, Coords::buedelsdorf(), 100, 10],
        ];
    }
}
