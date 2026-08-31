<?php declare(strict_types=1);

namespace Tests\Controller\Api\CycleApi;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\CitySlug;
use App\Entity\Ride;
use Tests\Controller\Api\AbstractApiControllerTestCase;

/**
 * Tests des Cycle-Delete-Endpunkts. Prüft insbesondere, dass zugehörige Rides
 * NICHT mitgelöscht werden. Transaktions-isoliert.
 */
class CycleApiDeleteTest extends AbstractApiControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $connection = $this->entityManager->getConnection();

        if ($connection->isTransactionActive()) {
            $connection->rollBack();
        }

        parent::tearDown();
    }

    private function createCityWithCycleAndRide(): array
    {
        $slug = 'cycle-del-' . substr(md5(uniqid('', true)), 0, 12);

        $city = new City();
        $city->setCity('Cyclestadt');
        $city->setTitle('Critical Mass Cyclestadt');
        $city->setCreatedAt(new \DateTime());
        $this->entityManager->persist($city);

        $citySlug = new CitySlug();
        $citySlug->setSlug($slug);
        $citySlug->setCity($city);
        $this->entityManager->persist($citySlug);
        $city->setMainSlug($citySlug);

        $cycle = new CityCycle();
        $cycle->setCity($city);
        $cycle->setDayOfWeek(5);
        $cycle->setWeekOfMonth(0);
        $this->entityManager->persist($cycle);

        $ride = new Ride();
        $ride->setCity($city);
        $ride->setDateTime(new \DateTime('2026-09-01 19:00:00'));
        $ride->setTitle('Critical Mass');
        $ride->setCycle($cycle);
        $this->entityManager->persist($ride);

        $this->entityManager->flush();

        return [$city, $cycle, $ride];
    }

    public function testDeleteCycleKeepsRides(): void
    {
        [$city, $cycle, $ride] = $this->createCityWithCycleAndRide();
        $cycleId = $cycle->getId();
        $rideId = $ride->getId();

        $this->client->request('DELETE', '/api/' . $city->getMainSlugString() . '/cycles/' . $cycleId);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $this->assertNull($this->entityManager->getRepository(CityCycle::class)->find($cycleId));
        $survivingRide = $this->entityManager->getRepository(Ride::class)->find($rideId);
        $this->assertNotNull($survivingRide, 'Ride must survive cycle deletion');
        $this->assertNull($survivingRide->getCycle());
    }

    public function testDeleteCycleRejectsForeignCity(): void
    {
        [, $cycle] = $this->createCityWithCycleAndRide();

        // Zweite Stadt, die den Cycle nicht besitzt.
        $otherSlug = 'cycle-other-' . substr(md5(uniqid('', true)), 0, 12);
        $otherCity = new City();
        $otherCity->setCity('Fremdstadt');
        $otherCity->setTitle('Critical Mass Fremdstadt');
        $otherCity->setCreatedAt(new \DateTime());
        $this->entityManager->persist($otherCity);
        $otherCitySlug = new CitySlug();
        $otherCitySlug->setSlug($otherSlug);
        $otherCitySlug->setCity($otherCity);
        $this->entityManager->persist($otherCitySlug);
        $otherCity->setMainSlug($otherCitySlug);
        $this->entityManager->flush();

        $this->client->request('DELETE', '/api/' . $otherCity->getMainSlugString() . '/cycles/' . $cycle->getId());

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}
