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

    /**
     * @dataProvider provideCenterCoordinatesAndOptions
     */
    public function testDistanceOrderDirection(
        float $centerLat,
        float $centerLon,
        int $radius,
        string $order
    ): void
    {
        $apiUri = sprintf(
            '/api/city?centerLatitude=%f&centerLongitude=%f&radius=%d&distanceOrderDirection=%s',
            $centerLat,
            $centerLon,
            $radius,
            $order
        );

        $client = static::createClient();
        $client->request('GET', $apiUri);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        $distances = array_map(
            fn($city) => $this->haversine($centerLat, $centerLon, $city['latitude'], $city['longitude']),
            $data
        );

        $sorted = $distances;
        $order === 'asc' ? sort($sorted) : rsort($sorted);

        $this->assertSame($sorted, $distances, 'Cities are not sorted by ascending distance.');
    }

    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public static function provideCenterCoordinatesAndOptions(): array
    {
        return [
            // Hamburg: 53.55, 10.0
            [53.55, 10.0, 1, 'asc'],
            [53.55, 10.0, 1, 'desc'],
            [53.55, 10.0, 10, 'asc'],
            [53.55, 10.0, 10, 'desc'],
            [53.55, 10.0, 100, 'asc'],
            [53.55, 10.0, 100, 'desc'],

            // Berlin: 52.52, 13.405
            [52.52, 13.405, 1, 'asc'],
            [52.52, 13.405, 1, 'desc'],
            [52.52, 13.405, 10, 'asc'],
            [52.52, 13.405, 10, 'desc'],
            [52.52, 13.405, 100, 'asc'],
            [52.52, 13.405, 100, 'desc'],

            // Köln: 50.9375, 6.9603
            [50.9375, 6.94, 1, 'asc'],
            [50.9375, 6.94, 1, 'desc'],
            [50.9375, 6.94, 10, 'asc'],
            [50.9375, 6.94, 10, 'desc'],
            [50.9375, 6.94, 100, 'asc'],
            [50.9375, 6.94, 100, 'desc'],
        ];
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

