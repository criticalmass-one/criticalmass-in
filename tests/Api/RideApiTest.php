<?php declare(strict_types=1);

namespace Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RideApiTest extends WebTestCase
{
    public function testDefaultListLength()
    {
        $client = static::createClient();
        $client->request('GET', '/api/ride');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        $this->assertLessThanOrEqual(10, count($data));
    }

    public function testCustomListLength()
    {
        $client = static::createClient();
        $client->request('GET', '/api/ride?size=25');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        $this->assertLessThanOrEqual(25, count($data));
    }

    public function testFilterByCitySlug()
    {
        $client = static::createClient();
        $client->request('GET', '/api/ride?citySlug=hamburg');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        // there is no city information in ride list, so we check for rides in hamburg area
        foreach ($data as $ride) {
            $this->assertGreaterThan(53.3, $ride['latitude']);
            $this->assertLessThan(53.8, $ride['latitude']);
            $this->assertGreaterThan(9.7, $ride['longitude']);
            $this->assertLessThan(10.3, $ride['longitude']);
        }
    }

    public function testFilterByDate()
    {
        $client = static::createClient();
        $client->request('GET', '/api/ride?year=2022&month=6&day=24');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        foreach ($data as $ride) {
            $date = new \DateTime('@' . $ride['date_time']);
            $this->assertEquals(2022, (int) $date->format('Y'));
            $this->assertEquals(6, (int) $date->format('n'));
            $this->assertEquals(24, (int) $date->format('j'));
        }
    }

    public function testFilterByRadius()
    {
        $client = static::createClient();
        $client->request('GET', '/api/ride?centerLatitude=53.55&centerLongitude=10.0&radius=20');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        foreach ($data as $ride) {
            $distance = $this->calculateDistance(53.55, 10.0, $ride['latitude'], $ride['longitude']);
            $this->assertLessThanOrEqual(20, $distance);
        }
    }

    public function testSortByEstimatedParticipantsDescending()
    {
        $client = static::createClient();
        $client->request('GET', '/api/ride?orderBy=estimatedParticipants&orderDirection=desc');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $participants = array_column($data, 'estimatedParticipants');
        $sorted = $participants;
        rsort($sorted);
        $this->assertEquals($sorted, $participants);
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
