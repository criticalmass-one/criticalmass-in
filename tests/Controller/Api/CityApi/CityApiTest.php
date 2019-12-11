<?php declare(strict_types=1);

namespace Tests\Controller\Api\CityApi;

use App\Entity\City;
use Tests\Controller\Api\AbstractApiControllerTest;

class CityApiTest extends AbstractApiControllerTest
{
    /**
     * @testdox Request Hamburg and check the result.
     */
    public function testCityHamburg(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/hamburg');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var City $actualCity */
        $actualCity = $this->deserializeEntity($client->getResponse()->getContent(), City::class);

        $this->assertEquals('Hamburg', $actualCity->getCity());
    }

    /**
     * @testdox Ask for a unknown city and retrieve 404.
     */
    public function testUnkownCity(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/asdf');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testCityHamburgRawResult(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/hamburg');

        $expectedContent = '{"slug":"hamburg","color":{"red":0,"green":0,"blue":0},"mainSlug":{"slug":"hamburg"},"name":"Hamburg","title":"Critical Mass Hamburg","description":null,"latitude":53.550556,"longitude":9.993333,"slugs":[{"slug":"hamburg"}],"socialNetworkProfiles":[],"cityPopulation":0,"punchLine":null,"longDescription":null,"timezone":"Europe\/Berlin","threadNumber":0,"postNumber":0}';

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertIdLessJsonEquals($expectedContent, $client->getResponse()->getContent());
    }
}
