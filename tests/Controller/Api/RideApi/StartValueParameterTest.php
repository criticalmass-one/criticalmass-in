<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use App\Entity\Ride;
use Tests\Controller\Api\AbstractApiControllerTest;

class StartValueParameterTest extends AbstractApiControllerTest
{
    /**
     * @testdox Providing a startValue without any orderBy is ignored.
     */
    public function testRideListWithStartValueParameterOnly(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?startValue=2022-07-01');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @testdox Requesting rides later than 2022-07-01.
     */
    public function testRideListWithStartValueAndOrderByParameterAscending(): void
    {
        $startDateTime = new \DateTime('2022-07-01');

        $client = static::createClient();

        $client->request('GET', sprintf('/api/ride?orderBy=dateTime&orderDirection=ASC&startValue=%s', $startDateTime->format('Y-m-d')));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertCount(10, $actualRideList);

        /** @var Ride $actualRide */
        foreach ($actualRideList as $actualRide) {
            $this->assertGreaterThanOrEqual($startDateTime, $actualRide->getDateTime());
        }
    }

    /**
     * @testdox Requesting rides prior than 2022-06-30.
     */
    public function testRideListWithStartValueAndOrderByParameterDescending(): void
    {
        $startDateTime = new \DateTime('2022-06-30');

        $client = static::createClient();

        $client->request('GET', sprintf('/api/ride?orderBy=dateTime&orderDirection=DESC&startValue=%s', $startDateTime->format('Y-m-d')));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertCount(10, $actualRideList);

        /** @var Ride $actualRide */
        foreach ($actualRideList as $actualRide) {
            $this->assertLessThanOrEqual($startDateTime, $actualRide->getDateTime());
        }
    }
}
