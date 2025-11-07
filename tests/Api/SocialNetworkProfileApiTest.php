<?php declare(strict_types=1);

namespace Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SocialNetworkProfileApiTest extends WebTestCase
{
    public function testSearchProfiles(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/socialnetwork-profiles?networkIdentifier=instagram');

        $this->assertResponseIsSuccessful();
        $items = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($items);
    }

    public function testCityProfilesList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/hamburg/socialnetwork-profiles');

        $this->assertResponseIsSuccessful();
        $items = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($items);
    }

    public function testCreateAndUpdateProfile(): void
    {
        $client = static::createClient();

        $handle = 'phpunit_'.bin2hex(random_bytes(4));

        // Create
        $payload = [
            'network_identifier' => 'instagram',
            'handle'             => $handle,
            'auto_fetch'         => true,
        ];

        $client->request(
            'PUT',
            '/api/hamburg/socialnetwork-profiles',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($payload)
        );

        $this->assertResponseIsSuccessful(); // 200 laut Controller
        $created = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $created);
        $id = $created['id'];

        // Update
        $created['auto_fetch'] = false;

        $client->request(
            'POST',
            sprintf('/api/hamburg/socialnetwork-profiles/%d', $id),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($created)
        );

        $this->assertResponseIsSuccessful();
        $updated = json_decode($client->getResponse()->getContent(), true);
        $this->assertFalse($updated['auto_fetch'] ?? true);
    }
}
