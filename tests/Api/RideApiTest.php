<?php declare(strict_types=1);

namespace Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RideApiTest extends WebTestCase
{
    public function testDefaultListLength(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/ride');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);
        $this->assertLessThanOrEqual(10, count($data));
    }

    public function testLimitSizeToTwentyfive(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/ride?size=25');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);
        $this->assertLessThanOrEqual(25, count($data));
    }

    public function testLimitSizeToFive(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/ride?size=5');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);
        $this->assertLessThanOrEqual(5, count($data));
    }

    public function testFilterByCitySlug(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/ride?citySlug=hamburg');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        // there is no city information in ride list, so we check for rides in hamburg area
        foreach ($data as $ride) {
            $this->assertGreaterThan(53.3, $ride['latitude']);
            $this->assertLessThan(53.8, $ride['latitude']);
            $this->assertGreaterThan(9.7, $ride['longitude']);
            $this->assertLessThan(10.3, $ride['longitude']);
        }
    }

    public function testFilterByYearMonthDay(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/ride?year=2022&month=6&day=24');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        foreach ($data as $ride) {
            $date = new \DateTime('@' . $ride['date_time']);
            $this->assertEquals(2022, (int) $date->format('Y'));
            $this->assertEquals(6, (int) $date->format('n'));
            $this->assertEquals(24, (int) $date->format('j'));
        }
    }

    public function testFilterByYearMonth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/ride?year=2015&month=8');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        foreach ($data as $ride) {
            $date = new \DateTime('@' . $ride['date_time']);
            $this->assertEquals(2015, (int) $date->format('Y'));
            $this->assertEquals(8, (int) $date->format('n'));
        }
    }

    public function testFilterByYear(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/ride?year=2019');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        foreach ($data as $ride) {
            $date = new \DateTime('@' . $ride['date_time']);
            $this->assertEquals(2019, (int) $date->format('Y'));
        }
    }

    public function testFilterByRadiusInHamburg(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/ride?centerLatitude=53.55&centerLongitude=10.0&radius=20');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        foreach ($data as $ride) {
            $distance = $this->calculateDistance(53.55, 10.0, $ride['latitude'], $ride['longitude']);
            $this->assertLessThanOrEqual(20, $distance);
        }
    }

    public function testFilterByRadiusInTheMiddleOfNowhere(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/ride?centerLatitude=90&centerLongitude=0&radius=20');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertEmpty($data);
    }

    /**
     * @dataProvider rideTypeProvider
     */
    public function testFilterByRideType(string $rideType): void
    {
        $apiUri = sprintf('/api/ride?rideType=%s', $rideType);

        $client = static::createClient();
        $client->request('GET', $apiUri);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        foreach ($data as $ride) {
            $this->assertSame(strtoupper($rideType), $ride['ride_type']);
        }
    }

    public function rideTypeProvider(): array
    {
        return [
            ['critical_mass'],
            ['kidical_mass'],
            ['night_ride'],
            //['lunch_ride'],
            //['dawn_ride'],
            //['dusk_ride'],
            ['demonstration'],
            ['alleycat'],
            ['tour'],
            ['event'],
        ];
    }

    /**
     * @dataProvider orderParameterProvider
     */
    public function testSortByOrderParameter(string $orderBy, string $direction, ?string $propertyName = null): void
    {
        $apiUri = sprintf('/api/ride?orderBy=%s&orderDirection=%s', $orderBy, $direction);

        $client = static::createClient();
        $client->request('GET', sprintf($apiUri));

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        if (!$propertyName) {
            $propertyName = $orderBy;
        }

        $values = array_column($data, $propertyName);

        if (in_array($orderBy, ['createdAt', 'updatedAt'])) {
            $values = array_map(fn($v) => (new \DateTime($v))->getTimestamp(), $values);
            $sorted = $values;
            if ($direction === 'asc') {
                sort($sorted);
            } else {
                rsort($sorted);
            }
        } elseif (is_string($values[0])) {
            $collator = new \Collator('de_DE');
            $sorted = $values;
            $collator->sort($sorted);
            if ($direction === 'desc') {
                $sorted = array_reverse($sorted);
            }
        } else {
            $sorted = $values;
            if ($direction === 'asc') {
                sort($sorted);
            } else {
                rsort($sorted);
            }
        }

        $this->assertSame($sorted, $values);
    }

    public function orderParameterProvider(): array
    {
        return [
            ['id', 'asc'],
            ['id', 'desc'],
            ['slug', 'desc'],
            ['title', 'asc'],
            ['title', 'desc'],
            ['latitude', 'desc'],
            ['longitude', 'desc'],
            ['estimatedParticipants', 'desc', 'estimated_participants'],
            ['estimatedDuration', 'desc', 'estimated_duration'],
            ['estimatedDistance', 'desc', 'estimated_distance'],
            ['views', 'asc'],
            ['views', 'desc'],
            ['dateTime', 'asc', 'date_time'],
            ['dateTime', 'desc', 'date_time'],
        ];
    }

    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // in km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
