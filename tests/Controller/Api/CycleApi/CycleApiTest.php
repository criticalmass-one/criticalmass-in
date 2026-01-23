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

        $cycles = $this->deserializeEntityList($this->client->getResponse()->getContent(), CityCycle::class);

        $this->assertNotEmpty($cycles);
        $this->assertContainsOnlyInstancesOf(CityCycle::class, $cycles);
    }

    public function testListCyclesForHamburg(): void
    {
        $this->client->request('GET', '/api/cycles', ['citySlug' => 'hamburg']);

        $this->assertResponseIsSuccessful();

        $cycles = $this->deserializeEntityList($this->client->getResponse()->getContent(), CityCycle::class);

        $this->assertNotEmpty($cycles);

        foreach ($cycles as $cycle) {
            $this->assertEquals('Hamburg', $cycle->getCity()->getCity());
        }
    }

    public function testListCyclesForBerlin(): void
    {
        $this->client->request('GET', '/api/cycles', ['citySlug' => 'berlin']);

        $this->assertResponseIsSuccessful();

        $cycles = $this->deserializeEntityList($this->client->getResponse()->getContent(), CityCycle::class);

        $this->assertNotEmpty($cycles);
    }

    public function testListCyclesValidNow(): void
    {
        $this->client->request('GET', '/api/cycles', ['validNow' => true]);

        $this->assertResponseIsSuccessful();

        $cycles = $this->deserializeEntityList($this->client->getResponse()->getContent(), CityCycle::class);

        $this->assertNotEmpty($cycles);
    }

    public function testListCyclesByDayOfWeek(): void
    {
        $this->client->request('GET', '/api/cycles', ['dayOfWeek' => CityCycle::DAY_FRIDAY]);

        $this->assertResponseIsSuccessful();

        $cycles = $this->deserializeEntityList($this->client->getResponse()->getContent(), CityCycle::class);

        $this->assertNotEmpty($cycles);

        foreach ($cycles as $cycle) {
            $this->assertEquals(CityCycle::DAY_FRIDAY, $cycle->getDayOfWeek());
        }
    }

    public function testListCyclesByWeekOfMonth(): void
    {
        $this->client->request('GET', '/api/cycles', ['weekOfMonth' => CityCycle::WEEK_LAST]);

        $this->assertResponseIsSuccessful();

        $cycles = $this->deserializeEntityList($this->client->getResponse()->getContent(), CityCycle::class);

        $this->assertNotEmpty($cycles);

        foreach ($cycles as $cycle) {
            $this->assertEquals(CityCycle::WEEK_LAST, $cycle->getWeekOfMonth());
        }
    }

    public function testCycleHasExpectedProperties(): void
    {
        $this->client->request('GET', '/api/cycles', ['citySlug' => 'hamburg']);

        $this->assertResponseIsSuccessful();

        $cycles = $this->deserializeEntityList($this->client->getResponse()->getContent(), CityCycle::class);

        $this->assertNotEmpty($cycles);

        /** @var CityCycle $cycle */
        $cycle = $cycles[0];

        $this->assertNotNull($cycle->getDayOfWeek());
        $this->assertNotNull($cycle->getWeekOfMonth());
        $this->assertNotNull($cycle->getTime());
        $this->assertNotNull($cycle->getLocation());
        $this->assertNotNull($cycle->getLatitude());
        $this->assertNotNull($cycle->getLongitude());
    }
}
