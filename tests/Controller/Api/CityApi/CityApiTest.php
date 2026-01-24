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

        /** @var City $actualCity */
        $actualCity = $this->deserializeEntity($client->getResponse()->getContent(), City::class);

        $this->assertEquals('Hamburg', $actualCity->getCity());
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

        // Verify the response contains expected Hamburg data
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals('hamburg', $content['slug']);
        $this->assertEquals('Hamburg', $content['name']);
        $this->assertEquals('Critical Mass Hamburg', $content['title']);
        $this->assertEquals('Europe/Berlin', $content['timezone']);

        // Verify slugs array is present
        $this->assertArrayHasKey('slugs', $content);
        $this->assertNotEmpty($content['slugs']);
    }
}
