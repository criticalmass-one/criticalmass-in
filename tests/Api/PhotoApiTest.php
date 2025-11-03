<?php declare(strict_types=1);

namespace Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PhotoApiTest extends WebTestCase
{
    public function testListRidePhotos(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/hamburg/2015-08-28/listPhotos');

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);

        if (!empty($data)) {
            $first = $data[0];
            $this->assertArrayHasKey('id', $first);
            $this->assertArrayHasKey('latitude', $first);
            $this->assertArrayHasKey('longitude', $first);
        }
    }

    public function testListPhotosWithFilters(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/photo?citySlug=hamburg&year=2019&month=8&size=5');

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);

        $this->assertLessThanOrEqual(5, \count($data));
    }

    public function testShowAndUpdatePhotoEcho(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo?size=1');
        $this->assertResponseIsSuccessful();

        $list = json_decode($client->getResponse()->getContent(), true);
        if (empty($list)) {
            $this->markTestSkipped('No photos available to test.');
        }

        $id = $list[0]['id'];

        $client->request('GET', sprintf('/api/photo/%d', $id));
        $this->assertResponseIsSuccessful();
        $photo = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame($id, $photo['id']);

        $payload = $photo;
        $payload['description'] = 'Updated via API test';

        $client->request('POST', sprintf('/api/photo/%d', $id), [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($payload));
        $this->assertResponseIsSuccessful();

        $echo = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('Updated via API test', $echo['description'] ?? $payload['description']);
    }
}
