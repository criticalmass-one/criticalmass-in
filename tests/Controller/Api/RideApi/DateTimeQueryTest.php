<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use App\Entity\Ride;
use Tests\Controller\Api\AbstractApiControllerTest;

class DateTimeQueryTest extends AbstractApiControllerTest
{
    /**
     * @testdox Fetch rides of 2050.
     */
    public function testRideListWithYear2050Parameter(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?year=2050');
        
        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var Ride $ride */
        foreach ($actualRideList as $ride) {
            $this->assertEquals(2050, $ride->getDateTime()->format('Y'));
        }
    }

    /**
     * @testdox Fetch rides for February 2016.
     */
    public function testRideListWithYear2016Month2Parameter(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?year=2016&month=2');

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var Ride $ride */
        foreach ($actualRideList as $ride) {
            $this->assertEquals('2016-02', $ride->getDateTime()->format('Y-m'));
        }
    }

    /**
     * @testdox Fetch rides for 2015-06-01.
     */
    public function testRideListWithYear2015Month6Day1Parameter(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?year=2015&month=6&day=1');

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var Ride $ride */
        foreach ($actualRideList as $ride) {
            $this->assertEquals('2015-06-01', $ride->getDateTime()->format('Y-m-d'));
        }
    }

    /**
     * @testdox Fetch rides for 2011-06-24.
     */
    public function testRideListWithYear2011Month6Day24Parameter(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?year=2011&month=6&day=24');

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var Ride $ride */
        foreach ($actualRideList as $ride) {
            $this->assertEquals('2011-06-24', $ride->getDateTime()->format('Y-m-d'));
        }
    }
}
