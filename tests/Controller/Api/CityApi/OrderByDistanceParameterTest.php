<?php declare(strict_types=1);

namespace Tests\Controller\Api\CityApi;

use App\Criticalmass\Geo\DistanceCalculator\DistanceCalculator;
use App\Entity\City;
use Caldera\GeoBasic\Coord\Coord;
use Tests\Controller\Api\AbstractApiControllerTest;

class OrderByDistanceParameterTest extends AbstractApiControllerTest
{
    /**
     * @testdox Get 10 cities ordered by title ascending.
     */
    public function testCityListOrderByTitleAscending(): void
    {
        $buedelsdorf = new Coord(54.318072, 9.696301);

        $client = static::createClient();

        $client->request('GET', sprintf('/api/city?centerLatitude=%f&centerLongitude=%f&distanceOrderDirection=ASC', $buedelsdorf->getLatitude(), $buedelsdorf->getLongitude()));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualCityList = $this->deserializeEntityList($client->getResponse()->getContent(), City::class);

        $this->assertCount(10, $actualCityList);

        $minDistance = null;

        /**
         * @var City $actualCity
         */
        foreach ($actualCityList as $actualCity) {
            $distance = DistanceCalculator::calculateDistance($buedelsdorf, $actualCity->getCoord());

            if ($minDistance) {
                $this->assertGreaterThanOrEqual($minDistance, $distance);
            }

            $minDistance = $distance;
        }
    }

    /**
     * @testdox Get 10 cities ordered by title descending.
     */
    public function testCityListOrderByDateTimeDescending(): void
    {
        $buedelsdorf = new Coord(54.318072, 9.696301);

        $client = static::createClient();

        $client->request('GET', sprintf('/api/city?centerLatitude=%f&centerLongitude=%f&distanceOrderDirection=DESC', $buedelsdorf->getLatitude(), $buedelsdorf->getLongitude()));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualCityList = $this->deserializeEntityList($client->getResponse()->getContent(), City::class);

        $this->assertCount(10, $actualCityList);

        $maxDistance = null;

        /**
         * @var City $actualCity
         */
        foreach ($actualCityList as $actualCity) {
            $distance = DistanceCalculator::calculateDistance($buedelsdorf, $actualCity->getCoord());

            if ($maxDistance) {
                $this->assertLessThanOrEqual($maxDistance, $distance);
            }

            $maxDistance = $distance;
        }
    }
}
