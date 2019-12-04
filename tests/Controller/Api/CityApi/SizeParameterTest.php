<?php declare(strict_types=1);

namespace Tests\Controller\Api\CityApi;

use App\Entity\City;
use Tests\Controller\Api\AbstractApiControllerTest;

class SizeParameterTest extends AbstractApiControllerTest
{
    /**
     * @testdox Calling api without size parameter delivers 10 results.
     */
    public function testCityListWithBoundingSizeParameter(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/city');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualCityList = $this->deserializeEntityList($client->getResponse()->getContent(), City::class);

        $this->assertCount(10, $actualCityList);
    }

    /**
     * @testdox Request 5 results.
     */
    public function testCityListWith5Results(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/city?size=5');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualCityList = $this->deserializeEntityList($client->getResponse()->getContent(), City::class);

        $this->assertCount(5, $actualCityList);
    }

    /**
     * @testdox Requesting 1 city will result in a list with 1 city.
     */
    public function testCityListWith1Result(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/city?size=1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualCityList = $this->deserializeEntityList($client->getResponse()->getContent(), City::class);

        $this->assertCount(1, $actualCityList);
    }

    /**
     * @testdox Calling size=0 will default to 10 results.
     */
    public function testCityListWithSize0Returning5Results(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/city?size=0');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualCityList = $this->deserializeEntityList($client->getResponse()->getContent(), City::class);

        $this->assertCount(10, $actualCityList);
    }

    /**
     * @testdox Calling size=-1 will default to 10 results.
     */
    public function testCityListWithNegativeParameter(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/city?size=-1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualCityList = $this->deserializeEntityList($client->getResponse()->getContent(), City::class);

        $this->assertCount(10, $actualCityList);
    }

    /**
     * @testdox Using strings as parameter value will default to 10 results.
     */
    public function testCityListWithInvalidParameter(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/city?size=abc');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualCityList = $this->deserializeEntityList($client->getResponse()->getContent(), City::class);

        $this->assertCount(10, $actualCityList);
    }
}
