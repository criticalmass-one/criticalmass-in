<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use App\Entity\Ride;
use Tests\Controller\Api\AbstractApiControllerTest;

class SizeParameterTest extends AbstractApiControllerTest
{
    /**
     * @testdox Calling api without size parameter delivers 10 results.
     */
    public function testRideListWithBoundingSizeParameter(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride');

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertCount(10, $actualRideList);
    }

    /**
     * @testdox Request 5 results.
     */
    public function testRideListWith5Results(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?size=5');

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertCount(5, $actualRideList);
    }

    /**
     * @testdox Request 15 rides.
     */
    public function testRideListWith15Results(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?size=15');

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertCount(15, $actualRideList);
    }

    /**
     * @testdox Requesting 1 ride will result in a list with 1 ride.
     */
    public function testRideListWith1Result(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?size=1');

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertCount(1, $actualRideList);
    }

    /**
     * @testdox Calling size=0 will default to 10 results.
     */
    public function testRideListWithSize0Returning10Results(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?size=0');

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertCount(10, $actualRideList);
    }

    /**
     * @testdox Calling size=-1 will default to 10 results.
     */
    public function testRideListWithNegativeParameter(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?size=-1');

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertCount(10, $actualRideList);
    }

    /**
     * @testdox Using strings as parameter value will default to 10 results.
     */
    public function testRideListWithInvalidParameter(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?size=abc');

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertCount(10, $actualRideList);
    }
}
