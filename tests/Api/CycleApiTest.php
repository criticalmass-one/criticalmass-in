<?php declare(strict_types=1);

namespace Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CycleApiTest extends WebTestCase
{
    public function testListCyclesByCitySlug(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/cycles?citySlug=hamburg&validNow=1');

        $this->assertResponseIsSuccessful();

        $items = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($items);

        if (!empty($items)) {
            $c = $items[0];
            $this->assertArrayHasKey('day_of_week', $c);
            $this->assertArrayHasKey('week_of_month', $c);
            $this->assertArrayHasKey('valid_from', $c);
            $this->assertArrayHasKey('valid_until', $c);
        }
    }

    public function testListCyclesWithDayAndWeekFilter(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/cycles?citySlug=hamburg&dayOfWeek=5&weekOfMonth=4');

        $this->assertResponseIsSuccessful();

        $items = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($items);
    }

    public function testListCyclesByRegionSlug(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/cycles?regionSlug=schleswig-holstein');

        $this->assertResponseIsSuccessful();
        $items = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($items);
    }
}
