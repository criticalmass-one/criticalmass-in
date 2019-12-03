<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use App\Entity\Ride;
use Tests\Controller\Api\AbstractApiControllerTest;

class BoundingBoxQueryTest extends AbstractApiControllerTest
{
    /**
     * @testdox This will test for any rides in a bounding box in Hamburg from Hamburg-Eidelstedt (northwest) to Hamburg-Hamm (southeast).
     */
    public function testRideListWithBoundingBoxQueryForHamburg(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?bbNorthLatitude=53.606153&bbWestLongitude=9.905992&bbSouthLatitude=53.547299&bbEastLongitude=10.054452');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertCount(10, $actualRideList);

        /** @var Ride $ride */
        foreach ($actualRideList as $ride) {
            $this->assertEquals(53.566676, $ride->getLatitude());
            $this->assertEquals(9.984711, $ride->getLongitude());
        }
    }

    /**
     * @testdox Invalid coords for bounding box query will be ignored and result in 10 random rides.
     */
    public function testRideListWithInvalidBoundingBoxQuery(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?bbNorthLatitude=54&bbSouthLatitude=57&bbEastLongitude=9&bbWestLongitude=10.054470');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertCount(10, $actualRideList);
    }

    /**
     * @testdox This will test for rides in a bounding box in Hamburg from Hamburg-Eidelstedt (northwest) to Hamburg-Hamm (southeast) on 2011-06-24.
     */
    public function testRideListWithBoundingBoxForHamburgAndYearMonthDayQuery(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?year=2011&month=6&day=24&bbNorthLatitude=53.606120&bbSouthLatitude=53.547127&bbWestLongitude=9.906029&bbEastLongitude=10.054470');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertCount(1, $actualRideList);

        /** @var Ride $actualRide */
        $actualRide = array_pop($actualRideList);

        $this->assertEquals(53.566676, $actualRide->getLatitude());
        $this->assertEquals(9.984711, $actualRide->getLongitude());
        $this->assertEquals(new \DateTime('2011-06-24 19:00:00'), $actualRide->getDateTime());
    }

    /**
     * @testdox This will test for rides in a bounding box in Hamburg from Hamburg-Eidelstedt (northwest) to Hamburg-Hamm (southeast) on 2011-06-24.
     */
    public function testRideListWithBoundingBoxQueryForLondon(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?bbNorthLatitude=51.527641&bbSouthLatitude=51.503026&bbWestLongitude=-0.153760&bbEastLongitude=0.003207');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertCount(10, $actualRideList);

        /** @var Ride $ride */
        foreach ($actualRideList as $ride) {
            $this->assertEquals(51.50762, $ride->getLatitude());
            $this->assertEquals(-0.114708, $ride->getLongitude());
        }
    }
}
