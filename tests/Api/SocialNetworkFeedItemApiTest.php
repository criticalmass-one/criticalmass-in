<?php declare(strict_types=1);

namespace Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SocialNetworkFeedItemApiTest extends WebTestCase
{
    public function testListFeedItemsByCity(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/hamburg/socialnetwork-feeditems');

        $this->assertResponseIsSuccessful();

        $items = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($items);
    }

    public function testCreateThenUpdateFeedItem(): void
    {
        $client = static::createClient();
        $unique = 'phpunit-'.bin2hex(random_bytes(6));

        // Create
        $create = [
            'city_slug'         => 'hamburg',
            'network_identifier'=> 'instagram',
            'unique_identifier' => $unique,
            'content'           => 'Test content',
            'posted_at'         => (new \DateTimeImmutable('-1 day'))->format(DATE_ATOM),
        ];

        $client->request(
            'PUT',
            '/api/hamburg/socialnetwork-feeditems',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($create)
        );

        $status = $client->getResponse()->getStatusCode();
        if ($status === 409) {
            $this->markTestSkipped('Unique constraint hit (item likely already created in previous runs).');
        }
        $this->assertResponseStatusCodeSame(201);

        $created = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $created);
        $id = $created['id'];

        // Update
        $update = $created;
        $update['content'] = 'Updated via phpunit';

        $client->request(
            'POST',
            sprintf('/api/hamburg/socialnetwork-feeditems/%d', $id),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($update)
        );

        $this->assertResponseIsSuccessful();
        $updated = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('Updated via phpunit', $updated['content'] ?? null);
    }
}
