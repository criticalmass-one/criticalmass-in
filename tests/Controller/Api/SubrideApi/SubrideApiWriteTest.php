<?php declare(strict_types=1);

namespace Tests\Controller\Api\SubrideApi;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Ride;
use App\Entity\Subride;
use Tests\Controller\Api\AbstractApiControllerTestCase;

/**
 * Tests der schreibenden Subride-Endpunkte (create/update/delete).
 * Transaktions-isoliert.
 */
class SubrideApiWriteTest extends AbstractApiControllerTestCase
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
        $slug = 'subride-api-' . substr(md5(uniqid('', true)), 0, 12);

        $city = new City();
        $city->setCity('Subridestadt');
        $city->setTitle('Critical Mass Subridestadt');
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

    private function addSubride(Ride $ride): Subride
    {
        $subride = new Subride();
        $subride->setRide($ride);
        $subride->setTitle('Anfahrt Nord');
        $subride->setLocation('Hauptbahnhof');
        $subride->setDateTime(new \DateTime('2026-09-01 18:00:00'));
        $subride->setCreatedAt(new \DateTime());
        $this->entityManager->persist($subride);
        $this->entityManager->flush();

        return $subride;
    }

    public function testCreateSubride(): void
    {
        [$city, $ride] = $this->createRide();

        $this->client->request('PUT', '/api/' . $city->getMainSlugString() . '/2026-09-01/subride', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['title' => 'Anfahrt Süd', 'location' => 'Südbahnhof', 'dateTime' => '2026-09-01 18:00:00']));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertCount(1, $this->entityManager->getRepository(Subride::class)->findBy(['ride' => $ride]));
    }

    public function testCreateSubrideRejectsMissingLocation(): void
    {
        [$city] = $this->createRide();

        $this->client->request('PUT', '/api/' . $city->getMainSlugString() . '/2026-09-01/subride', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['title' => 'Ohne Ort', 'dateTime' => '2026-09-01 18:00:00']));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdateSubride(): void
    {
        [$city, $ride] = $this->createRide();
        $subride = $this->addSubride($ride);
        $subrideId = $subride->getId();

        $this->client->request('POST', '/api/' . $city->getMainSlugString() . '/2026-09-01/' . $subrideId, [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['title' => 'Anfahrt West']));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $updated = $this->entityManager->getRepository(Subride::class)->find($subrideId);
        $this->assertSame('Anfahrt West', $updated?->getTitle());
    }

    public function testDeleteSubride(): void
    {
        [$city, $ride] = $this->createRide();
        $subride = $this->addSubride($ride);
        $subrideId = $subride->getId();

        $this->client->request('DELETE', '/api/' . $city->getMainSlugString() . '/2026-09-01/' . $subrideId);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $this->assertNull($this->entityManager->getRepository(Subride::class)->find($subrideId));
    }
}
