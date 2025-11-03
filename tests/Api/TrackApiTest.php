<?php declare(strict_types=1);

namespace Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrackApiTest extends WebTestCase
{
    public function testListTracksForRide(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/hamburg/2015-08-28/listTracks');

        $this->assertResponseIsSuccessful();

        $items = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($items);
    }

    public function testViewTrackFromList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/hamburg/2015-08-28/listTracks');

        $this->assertResponseIsSuccessful();
        $list = json_decode($client->getResponse()->getContent(), true);

        if (empty($list)) {
            $this->markTestSkipped('No tracks available.');
        }

        $id = $list[0]['id'];

        $client->request('GET', sprintf('/api/track/%d', $id));
        $this->assertResponseIsSuccessful();

        $track = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame($id, $track['id']);
    }

    public function testListTracksGlobal(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/track?citySlug=hamburg&size=5');

        $this->assertResponseIsSuccessful();
        $items = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($items);
        $this->assertLessThanOrEqual(5, \count($items));
    }

    public function testDeleteTrackRequiresPermission(): void
    {
        $client = static::createClient();
        // irgendeine Track-ID – wir versuchen erst eine zu holen
        $client->request('GET', '/api/track?size=1');
        $this->assertResponseIsSuccessful();
        $list = json_decode($client->getResponse()->getContent(), true);

        if (empty($list)) {
            $this->markTestSkipped('No tracks to delete.');
        }

        $id = $list[0]['id'];

        $client->request('DELETE', sprintf('/api/track/%d', $id));
        $this->assertTrue(
            \in_array($client->getResponse()->getStatusCode(), [401, 403], true),
            'Expected 401/403 when deleting without proper permissions.'
        );
    }
}
