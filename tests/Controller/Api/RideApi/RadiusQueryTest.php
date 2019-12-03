<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use App\Entity\Ride;
use Tests\Controller\Api\AbstractApiControllerTest;

class RadiusQueryTest extends AbstractApiControllerTest
{
    /**
     * @testdox There are no rides in a 10 kilometer radius around Kiel.
     */
    public function testRideListWithRadiusQueryAroundKielWithin10Kilometers(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?centerLatitude=54.343024&centerLongitude=10.129730&radius=10');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertCount(0, $actualRideList);
    }

    /**
     * @testdox Look up rides in a 200 kilometer radius around Kiel.
     */
    public function testRideListWithRadiusQueryAroundKielWithin200Kilometers(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?centerLatitude=54.343024&centerLongitude=10.129730&radius=200');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertCount(10, $actualRideList);
    }

    /**
     * @testdox Look up rides within 10 kilometers around Hamburg.
     */
    public function testRideListWithRadiusQueryAroundHamburg(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?centerLatitude=53.550823&centerLongitude=9.993163&radius=10');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertCount(10, $actualRideList);

        /** @var Ride $actualRide */
        foreach ($actualRideList as $actualRide) {
            $this->assertEquals(53.566676, $actualRide->getLatitude());
            $this->assertEquals(9.984711, $actualRide->getLongitude());
        }
    }
}
