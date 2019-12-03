<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use App\Entity\Ride;
use Tests\Controller\Api\AbstractApiControllerTest;

class OrderByParameterTest extends AbstractApiControllerTest
{
    /**
     * @testdox Get 10 rides ordered by dateTime ascending.
     */
    public function testRideListOrderByDateTimeAscending(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?orderBy=dateTime&orderDirection=ASC');

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(10, $actualRideList);
    }

    /**
     * @testdox Get 10 rides ordered by dateTime descending.
     */
    public function testRideListOrderByDateTimeDescending(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?orderBy=dateTime&orderDirection=DESC');

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(10, $actualRideList);
    }

    /**
     * @testdox Providing invalid order direction will not break things.
     */
    public function testRideListOrderByDateTimeInvalidOrder(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?orderBy=dateTime&orderDirection=FOO');

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(10, $actualRideList);
    }

    /**
     * @testdox Providing invalid fields will not break api.
     */
    public function testRideListOrderByInvalidOrder(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?orderBy=invalidField&orderDirection=DESC');

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(10, $actualRideList);
    }
}
