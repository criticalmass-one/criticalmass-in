<?php declare(strict_types=1);

namespace Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CityApiTest extends WebTestCase
{
    public function testFindByName(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/city?name=hamburg');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame(1, count($data));

        $hamburg = $data[0];

        $this->assertStringContainsStringIgnoringCase('hamburg', $hamburg['name']);
    }

    public function testFilterByRegion(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/city?regionSlug=schleswig-holstein');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        foreach ($data as $city) {
            $this->assertGreaterThanOrEqual(53.3, $city['latitude']);
            $this->assertLessThanOrEqual(55.1, $city['latitude']);
            $this->assertGreaterThanOrEqual(8.3, $city['longitude']);
            $this->assertLessThanOrEqual(11.3, $city['longitude']);
        }
    }

    public function testLimitSize(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/city?size=5');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        $this->assertCount(5, $data);
    }

    public function testBoundingBox(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/city?bbNorthLatitude=54&bbSouthLatitude=53&bbWestLongitude=9.8&bbEastLongitude=10.2');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        foreach ($data as $city) {
            $this->assertGreaterThanOrEqual(53, $city['latitude']);
            $this->assertLessThanOrEqual(54, $city['latitude']);
            $this->assertGreaterThanOrEqual(9.8, $city['longitude']);
            $this->assertLessThanOrEqual(10.2, $city['longitude']);
        }
    }

    public function testDistanceOrderDirection(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/city?centerLatitude=53.55&centerLongitude=10.0&radius=50&distanceOrderDirection=asc');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);
    }

    public function testStartValue(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/city?orderBy=id&orderDirection=asc&startValue=100');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        foreach ($data as $city) {
            $this->assertGreaterThanOrEqual(100, $city['id']);
        }
    }

    /**
     * @dataProvider cityOrderProvider
     */
    public function testOrderByParameter(string $orderBy, string $direction): void
    {
        $client = static::createClient();
        $client->request('GET', sprintf('/api/city?orderBy=%s&orderDirection=%s', $orderBy, $direction));

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        $values = array_column($data, $orderBy);
        $sorted = $values;

        if ($direction === 'asc') {
            sort($sorted);
        } else {
            rsort($sorted);
        }

        $this->assertSame($sorted, $values);
    }

    public function cityOrderProvider(): array
    {
        return [
            ['id', 'asc'], ['id', 'desc'],
            ['region', 'asc'], ['region', 'desc'],
            ['name', 'asc'], ['name', 'desc'],
            ['title', 'asc'], ['title', 'desc'],
            ['cityPopulation', 'asc'], ['cityPopulation', 'desc'],
            ['latitude', 'asc'], ['latitude', 'desc'],
            ['longitude', 'asc'], ['longitude', 'desc'],
            ['updatedAt', 'asc'], ['updatedAt', 'desc'],
            ['createdAt', 'asc'], ['createdAt', 'desc'],
        ];
    }
}
