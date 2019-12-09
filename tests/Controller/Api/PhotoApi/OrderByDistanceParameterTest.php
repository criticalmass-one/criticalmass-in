<?php declare(strict_types=1);

namespace Tests\Controller\Api\PhotoApi;

use App\Criticalmass\Geo\DistanceCalculator\DistanceCalculator;
use App\Entity\Photo;
use Caldera\GeoBasic\Coord\Coord;
use Tests\Controller\Api\AbstractApiControllerTest;

class OrderByDistanceParameterTest extends AbstractApiControllerTest
{
    /**
     * @testdox Get 10 photos ordered by distance ascending.
     */
    public function testPhotoListOrderByDistanceAscending(): void
    {
        $buedelsdorf = new Coord(54.318072, 9.696301);

        $client = static::createClient();

        $client->request('GET', sprintf('/api/photo?centerLatitude=%f&centerLongitude=%f&distanceOrderDirection=ASC', $buedelsdorf->getLatitude(), $buedelsdorf->getLongitude()));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        $this->assertCount(10, $actualPhotoList);

        $minDistance = null;

        /**
         * @var Photo $actualPhoto
         */
        foreach ($actualPhotoList as $actualPhoto) {
            $distance = DistanceCalculator::calculateDistance($buedelsdorf, $actualPhoto->getCoord());

            if ($minDistance) {
                $this->assertGreaterThanOrEqual($minDistance, $distance);
            }

            $minDistance = $distance;
        }
    }

    /**
     * @testdox Get 10 photos ordered by distance descending.
     */
    public function testPhotoListOrderByDistanceDescending(): void
    {
        $buedelsdorf = new Coord(54.318072, 9.696301);

        $client = static::createClient();

        $client->request('GET', sprintf('/api/photo?centerLatitude=%f&centerLongitude=%f&distanceOrderDirection=DESC', $buedelsdorf->getLatitude(), $buedelsdorf->getLongitude()));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        $this->assertCount(10, $actualPhotoList);

        $maxDistance = null;

        /**
         * @var Photo $actualPhoto
         */
        foreach ($actualPhotoList as $actualPhoto) {
            $distance = DistanceCalculator::calculateDistance($buedelsdorf, $actualPhoto->getCoord());

            if ($maxDistance) {
                $this->assertLessThanOrEqual($maxDistance, $distance);
            }

            $maxDistance = $distance;
        }
    }
}
