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
     * @testdox There is Kiel and Hamburg around 100 kilometers of Buedelsdorf.
     */
    public function testThereAre2CitiesWithin100KilometersAroundBuedelsdorf(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/city?centerLatitude=54.318072&centerLongitude=9.696301&radius=100');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualCityList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertCount(2, $actualCityList);
    }

    /**
     * @testdox There are 5 cities in a 250 kilometer radius around Buedelsdorf.
     */
    public function testThereAre5CitiesWithin250KilometersAroundBuedelsdorf(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/city?centerLatitude=54.318072&centerLongitude=9.696301&radius=250');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualCityList = $this->deserializeEntityList($client->getResponse()->getContent(), City::class);

        $this->assertCount(5, $actualCityList);
    }
}
