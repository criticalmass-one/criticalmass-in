<?php declare(strict_types=1);

namespace Tests\Controller\Api\Parameter;

use App\Criticalmass\Geo\DistanceCalculator\DistanceCalculator;
use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Ride;
use App\EntityInterface\CoordinateInterface;
use App\Geo\Coord\CoordInterface;
use Tests\Controller\Api\AbstractApiControllerTest;
use Tests\Coords;

class OrderByDistanceParameterTest extends AbstractApiControllerTest
{
    /**
     * @dataProvider apiClassProvider
     */
    public function testResultListOrderByAscending(string $fqcn, CoordInterface $centerCoord): void
    {
        $client = static::createClient();

        $uri = sprintf('%s?centerLatitude=%f&centerLongitude=%f&distanceOrderDirection=ASC', $this->getApiEndpointForFqcn($fqcn), $centerCoord->getLatitude(), $centerCoord->getLongitude());

        $client->request('GET', $uri);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        /** @var float $minDistance */
        $minDistance = null;

        /** @var CoordinateInterface $result */
        foreach ($resultList as $result) {
            $distance = DistanceCalculator::calculateDistance($centerCoord, $result->toCoord());

            if ($minDistance) {
                $this->assertGreaterThanOrEqual($minDistance, $distance);
            }

            $minDistance = $distance;
        }
    }

    /**
     * @dataProvider apiClassProvider
     */
    public function testResultListOrderByDescending(string $fqcn, CoordInterface $centerCoord): void
    {
        $client = static::createClient();

        $uri = sprintf('%s?centerLatitude=%f&centerLongitude=%f&distanceOrderDirection=DESC', $this->getApiEndpointForFqcn($fqcn), $centerCoord->getLatitude(), $centerCoord->getLongitude());

        $client->request('GET', $uri);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);
        
        /** @var float $maxDistance */
        $maxDistance = null;

        /** @var CoordinateInterface $result */
        foreach ($resultList as $result) {
            $distance = DistanceCalculator::calculateDistance($centerCoord, $result->toCoord());

            if ($maxDistance) {
                $this->assertLessThanOrEqual($maxDistance, $distance);
            }

            $maxDistance = $distance;
        }
    }

    public function apiClassProvider(): array
    {
        return [
            [City::class, Coords::esslingen()],
            [Ride::class, Coords::hamburg()],
            [Photo::class, Coords::berlin()],
        ];
    }
}
