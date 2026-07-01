<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Ride;
use Tests\Controller\Api\AbstractApiControllerTestCase;

/**
 * Tests des Ride enable/disable-Endpunkts. Transaktions-isoliert.
 */
class RideApiEnabledTest extends AbstractApiControllerTestCase
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

    private function createRide(): array
    {
        $slug = 'ride-enabled-' . substr(md5(uniqid('', true)), 0, 12);

        $city = new City();
        $city->setCity('Ridestadt');
        $city->setTitle('Critical Mass Ridestadt');
        $city->setCreatedAt(new \DateTime());
        $this->entityManager->persist($city);

        $citySlug = new CitySlug();
        $citySlug->setSlug($slug);
        $citySlug->setCity($city);
        $this->entityManager->persist($citySlug);
        $city->setMainSlug($citySlug);

        $ride = new Ride();
        $ride->setCity($city);
        $ride->setDateTime(new \DateTime('2026-09-01 19:00:00'));
        $ride->setTitle('Critical Mass');
        $this->entityManager->persist($ride);

        $this->entityManager->flush();

        return [$city, $ride];
    }

    public function testDisableRide(): void
    {
        [$city, $ride] = $this->createRide();
        $rideId = $ride->getId();

        $this->client->request('POST', '/api/' . $city->getMainSlugString() . '/2026-09-01/enabled', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['enabled' => false]));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $reloaded = $this->entityManager->getRepository(Ride::class)->find($rideId);
        $this->assertFalse($reloaded?->isEnabled());
    }

    public function testSetRideEnabledRejectsMissingFlag(): void
    {
        [$city] = $this->createRide();

        $this->client->request('POST', '/api/' . $city->getMainSlugString() . '/2026-09-01/enabled', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['foo' => 'bar']));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }
}
