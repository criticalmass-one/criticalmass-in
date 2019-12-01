<?php declare(strict_types=1);

namespace Tests\Controller\Api;

class CityApiTest extends AbstractApiControllerTest
{
    public function testCityHamburg(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/hamburg');

        $expectedContent = '{"slug":"hamburg","id":7,"mainSlug":{"id":7,"slug":"hamburg"},"name":"Hamburg","title":"Critical Mass Hamburg","description":null,"url":null,"facebook":null,"twitter":null,"latitude":0,"longitude":0,"slugs":[{"id":7,"slug":"hamburg"}],"cityPopulation":0,"punchLine":null,"longDescription":null,"timezone":"Europe\/Berlin","threadNumber":0,"postNumber":0,"colorRed":0,"colorGreen":0,"colorBlue":0}';

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertIdLessJsonEquals($expectedContent, $client->getResponse()->getContent());
    }
}