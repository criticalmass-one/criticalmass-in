<?php declare(strict_types=1);

namespace Tests\Controller\Api\CityApi;

use App\Entity\City;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTest;

class CityApiTest extends AbstractApiControllerTest
{
    #[TestDox('Request Hamburg and check the result.')]
    public function testCityHamburg(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/hamburg');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = json_decode($client->getResponse()->getContent(), true);

        // Note: City entity uses #[SerializedName('name')] on the $city property
        $this->assertEquals('Hamburg', $data['name']);
    }

    #[TestDox('Ask for a unknown city and retrieve 404.')]
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

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = json_decode($client->getResponse()->getContent(), true);

        // Check essential properties
        // Note: City entity uses #[SerializedName('name')] on the $city property
        // and #[SerializedName('slug')] on getMainSlugString()
        $this->assertEquals('hamburg', $data['slug']);
        $this->assertEquals('Hamburg', $data['name']);
        $this->assertEquals('Critical Mass Hamburg', $data['title']);
        $this->assertEquals('Europe/Berlin', $data['timezone']);
        $this->assertArrayHasKey('main_slug', $data);
        $this->assertEquals('hamburg', $data['main_slug']['slug']);
        $this->assertArrayHasKey('latitude', $data);
        $this->assertArrayHasKey('longitude', $data);
        $this->assertArrayHasKey('slugs', $data);
        $this->assertArrayHasKey('color', $data);
    }
}
