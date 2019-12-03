<?php declare(strict_types=1);

namespace Tests\Controller\Api\CityApi;

use App\Entity\City;
use App\Entity\Ride;
use Tests\Controller\Api\AbstractApiControllerTest;

class RadiusQueryTest extends AbstractApiControllerTest
{
    /**
     * @testdox There are no cities in a 10 kilometer radius around Buedelsdorf.
     */
    public function testThereAreNoCitiesWithin10KilometersAroundBuedelsdorf(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/city?centerLatitude=54.318072&centerLongitude=9.696301&radius=10');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualCityList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertCount(0, $actualCityList);
    }

    /**
     * @testdox There is Kiel around 35 kilometers of Hamburg.
     */
    public function testThereIsHamburgWithin100KilometersAroundBuedelsdorf(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/city?centerLatitude=54.318072&centerLongitude=9.696301&radius=100');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualCityList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertCount(1, $actualCityList);
    }

    /**
     * @testdox There are Berlin, Mainz and Hamburg in a 500 kilometer radius around Buedelsdorf.
     */
    public function testThereAreThreeCitiesWithin500KilometersAroundBuedelsdorf(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/city?centerLatitude=54.318072&centerLongitude=9.696301&radius=500');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualCityList = $this->deserializeEntityList($client->getResponse()->getContent(), City::class);

        $this->assertCount(3, $actualCityList);
    }
}
