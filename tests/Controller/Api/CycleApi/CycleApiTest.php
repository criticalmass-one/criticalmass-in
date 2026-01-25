<?php declare(strict_types=1);

namespace Tests\Controller\Api\CycleApi;

use App\Entity\CityCycle;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class CycleApiTest extends AbstractApiControllerTestCase
{
    public function testListCycles(): void
    {
        $this->client->request('GET', '/api/cycles');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response[0]);
    }

    public function testListCyclesForHamburg(): void
    {
        $this->client->request('GET', '/api/cycles', ['citySlug' => 'hamburg']);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);

        foreach ($response as $cycle) {
            $this->assertArrayHasKey('city', $cycle);
            $this->assertEquals('Hamburg', $cycle['city']['name']);
        }
    }

    public function testListCyclesForBerlin(): void
    {
        $this->client->request('GET', '/api/cycles', ['citySlug' => 'berlin']);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
    }

    public function testListCyclesValidNow(): void
    {
        $this->client->request('GET', '/api/cycles', ['validNow' => true]);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
    }

    public function testListCyclesByDayOfWeek(): void
    {
        $this->client->request('GET', '/api/cycles', ['dayOfWeek' => CityCycle::DAY_FRIDAY]);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);

        foreach ($response as $cycle) {
            $this->assertEquals(CityCycle::DAY_FRIDAY, $cycle['day_of_week']);
        }
    }

    public function testListCyclesByWeekOfMonth(): void
    {
        $this->client->request('GET', '/api/cycles', ['weekOfMonth' => CityCycle::WEEK_LAST]);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);

        foreach ($response as $cycle) {
            $this->assertEquals(CityCycle::WEEK_LAST, $cycle['week_of_month']);
        }
    }

    public function testCycleHasExpectedProperties(): void
    {
        $this->client->request('GET', '/api/cycles', ['citySlug' => 'hamburg']);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);

        $cycle = $response[0];

        $this->assertArrayHasKey('day_of_week', $cycle);
        $this->assertNotNull($cycle['day_of_week']);
        $this->assertArrayHasKey('week_of_month', $cycle);
        $this->assertNotNull($cycle['week_of_month']);
        $this->assertArrayHasKey('time', $cycle);
        $this->assertNotNull($cycle['time']);
        $this->assertArrayHasKey('location', $cycle);
        $this->assertNotNull($cycle['location']);
        $this->assertArrayHasKey('latitude', $cycle);
        $this->assertNotNull($cycle['latitude']);
        $this->assertArrayHasKey('longitude', $cycle);
        $this->assertNotNull($cycle['longitude']);
    }
}
