<?php declare(strict_types=1);

namespace Tests\Controller\Api;

class CityApiTest extends AbstractApiControllerTest
{
    public function testCityHamburg(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/hamburg');

        $expectedContent = '{"slug":"hamburg","color":{"red":0,"green":0,"blue":0},"mainSlug":{"slug":"hamburg"},"name":"Hamburg","title":"Critical Mass Hamburg","description":null,"latitude":53.550556,"longitude":9.993333,"slugs":[{"slug":"hamburg"}],"socialNetworkProfiles":[],"cityPopulation":0,"punchLine":null,"longDescription":null,"timezone":"Europe\/Berlin","threadNumber":0,"postNumber":0}';

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertIdLessJsonEquals($expectedContent, $client->getResponse()->getContent());
    }
}