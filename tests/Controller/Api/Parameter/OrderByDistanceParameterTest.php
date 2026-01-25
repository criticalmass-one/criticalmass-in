<?php declare(strict_types=1);

namespace Tests\Controller\Api\Parameter;

use App\Criticalmass\Geo\Coord\CoordInterface;
use App\Criticalmass\Geo\DistanceCalculator\DistanceCalculator;
use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Ride;
use App\EntityInterface\CoordinateInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Controller\Api\AbstractApiControllerTestCase;
use Tests\Coords;

class OrderByDistanceParameterTest extends AbstractApiControllerTestCase
{
    #[DataProvider('apiClassProvider')]
    public function testResultListOrderByAscending(string $fqcn, CoordInterface $centerCoord): void
    {

        $uri = sprintf('%s?centerLatitude=%f&centerLongitude=%f&distanceOrderDirection=ASC', $this->getApiEndpointForFqcn($fqcn), $centerCoord->getLatitude(), $centerCoord->getLongitude());

        $this->client->request('GET', $uri);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($this->client->getResponse()->getContent(), $fqcn);

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

    #[DataProvider('apiClassProvider')]
    public function testResultListOrderByDescending(string $fqcn, CoordInterface $centerCoord): void
    {

        $uri = sprintf('%s?centerLatitude=%f&centerLongitude=%f&distanceOrderDirection=DESC', $this->getApiEndpointForFqcn($fqcn), $centerCoord->getLatitude(), $centerCoord->getLongitude());

        $this->client->request('GET', $uri);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($this->client->getResponse()->getContent(), $fqcn);
        
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

    public static function apiClassProvider(): array
    {
        return [
            [City::class, Coords::esslingen()],
            [Ride::class, Coords::hamburg()],
            [Photo::class, Coords::berlin()],
        ];
    }
}
