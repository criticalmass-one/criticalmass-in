<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use App\Criticalmass\Geo\DistanceCalculator\DistanceCalculator;
use App\Entity\Ride;
use Tests\Controller\Api\AbstractApiControllerTest;
use Tests\Coords;

class OrderByDistanceParameterTest extends AbstractApiControllerTest
{
    /**
     * @testdox Get rides ordered by their distance starting in Hamburg. The query is limited to 2020-01-01 to avoid non-testable mess.
     */
    public function testRideListOrderByDateTimeAscending(): void
    {
        $client = static::createClient();

        $hamburg = Coords::hamburg();

        $uri = sprintf('/api/ride?year=2020&month=1&day=1&centerLatitude=%f&centerLongitude=%f&distanceOrderDirection=ASC', $hamburg->getLatitude(), $hamburg->getLongitude());

        $client->request('GET', $uri);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        /** @var float $minDistance */
        $minDistance = null;

        /** @var Ride $actualRide */
        foreach ($actualRideList as $actualRide) {
            $distance = DistanceCalculator::calculateDistance($hamburg, $actualRide->toCoord());

            if ($minDistance) {
                $this->assertGreaterThan($minDistance, $distance);
            }

            $minDistance = $distance;
        }
    }

    /**
     * @testdox Get rides ordered by their distance ending in Hamburg. The query is limited to 2020-01-01 to avoid non-testable mess.
     */
    public function testRideListOrderByDateTimeDescending(): void
    {
        $client = static::createClient();

        $hamburg = Coords::hamburg();

        $uri = sprintf('/api/ride?year=2020&month=1&day=1&centerLatitude=%f&centerLongitude=%f&distanceOrderDirection=DESC', $hamburg->getLatitude(), $hamburg->getLongitude());

        $client->request('GET', $uri);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);
        
        /** @var float $maxDistance */
        $maxDistance = null;

        /** @var Ride $actualRide */
        foreach ($actualRideList as $actualRide) {
            $distance = DistanceCalculator::calculateDistance($hamburg, $actualRide->toCoord());

            if ($maxDistance) {
                $this->assertLessThan($maxDistance, $distance);
            }

            $maxDistance = $distance;
        }
    }
}
