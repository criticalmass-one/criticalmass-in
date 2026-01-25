<?php declare(strict_types=1);

namespace Tests\Controller\Api\CityApi;

use App\Entity\City;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class CityApiTest extends AbstractApiControllerTestCase
{
    #[TestDox('Request Hamburg and check the result.')]
    public function testCityHamburg(): void
    {

        $this->client->request('GET', '/api/hamburg');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $data = json_decode($this->client->getResponse()->getContent(), true);

        // Note: City entity uses #[SerializedName('name')] on the $city property
        $this->assertEquals('Hamburg', $data['name']);
    }

    #[TestDox('Ask for a unknown city and retrieve 404.')]
    public function testUnkownCity(): void
    {

        $this->client->request('GET', '/api/asdf');

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testCityHamburgRawResult(): void
    {

        $this->client->request('GET', '/api/hamburg');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $data = json_decode($this->client->getResponse()->getContent(), true);

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
        // Verify the response contains expected Hamburg data
        $content = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals('hamburg', $content['slug']);
        $this->assertEquals('Hamburg', $content['name']);
        $this->assertEquals('Critical Mass Hamburg', $content['title']);
        $this->assertEquals('Europe/Berlin', $content['timezone']);

        // Verify slugs array is present
        $this->assertArrayHasKey('slugs', $content);
        $this->assertNotEmpty($content['slugs']);
    }
}
