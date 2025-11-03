<?php declare(strict_types=1);

namespace Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SubrideApiTest extends WebTestCase
{
    public function testListSubridesForRide(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/hamburg/2015-08-28/subride');

        $this->assertResponseIsSuccessful();

        $items = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($items);
    }

    public function testShowSubrideFromList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/hamburg/2015-08-28/subride');

        $this->assertResponseIsSuccessful();
        $list = json_decode($client->getResponse()->getContent(), true);

        if (empty($list)) {
            $this->markTestSkipped('No subrides to show.');
        }

        $id = $list[0]['id'];
        $client->request('GET', sprintf('/api/hamburg/2015-08-28/%d', $id));
        $this->assertResponseIsSuccessful();

        $one = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame($id, $one['id']);
    }
}
