<?php declare(strict_types=1);

namespace Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LocationApiTest extends WebTestCase
{
    public function testListLocationsForCity(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/hamburg/location');

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);

        if (!empty($data)) {
            $loc = $data[0];
            $this->assertArrayHasKey('slug', $loc);
            $this->assertArrayHasKey('name', $loc);
        }
    }

    public function testShowLocationFromList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/hamburg/location');

        $this->assertResponseIsSuccessful();
        $list = json_decode($client->getResponse()->getContent(), true);

        if (empty($list)) {
            $this->markTestSkipped('No locations available to test.');
        }

        $slug = $list[0]['slug'];
        $client->request('GET', sprintf('/api/hamburg/location/%s', $slug));
        $this->assertResponseIsSuccessful();

        $one = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame($slug, $one['slug']);
    }
}
