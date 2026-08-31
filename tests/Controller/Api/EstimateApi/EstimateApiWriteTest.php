<?php declare(strict_types=1);

namespace Tests\Controller\Api\EstimateApi;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Ride;
use App\Entity\RideEstimate;
use Tests\Controller\Api\AbstractApiControllerTestCase;

/**
 * Tests der schreibenden Estimate-Endpunkte (Liste, Update, Delete). Jede
 * Testmethode läuft in einer zurückgerollten Transaktion.
 */
class EstimateApiWriteTest extends AbstractApiControllerTestCase
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

    private function createRideWithEstimate(int $participants = 100): array
    {
        $slug = 'estimate-api-' . substr(md5(uniqid('', true)), 0, 12);

        $city = new City();
        $city->setCity('Schätzstadt');
        $city->setTitle('Critical Mass Schätzstadt');
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

        $estimate = new RideEstimate();
        $estimate->setRide($ride);
        $estimate->setEstimatedParticipants($participants);
        $estimate->setDateTime(new \DateTime('2026-09-01 19:30:00'));
        $estimate->setSource('api-test');
        $this->entityManager->persist($estimate);

        $this->entityManager->flush();

        return [$city, $ride, $estimate];
    }

    public function testListEstimatesReturnsId(): void
    {
        [$city, , $estimate] = $this->createRideWithEstimate();

        $this->client->request('GET', '/api/' . $city->getMainSlugString() . '/2026-09-01/estimates');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $ids = array_column($data['estimates'], 'id');
        $this->assertContains($estimate->getId(), $ids);
    }

    public function testUpdateEstimateChangesParticipants(): void
    {
        [, , $estimate] = $this->createRideWithEstimate(100);
        $estimateId = $estimate->getId();

        $this->client->request('POST', '/api/estimate/' . $estimateId, [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['estimation' => 777]));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $updated = $this->entityManager->getRepository(RideEstimate::class)->find($estimateId);
        $this->assertSame(777, $updated?->getEstimatedParticipants());
    }

    public function testDeleteEstimateRemovesIt(): void
    {
        [, , $estimate] = $this->createRideWithEstimate();
        $estimateId = $estimate->getId();

        $this->client->request('DELETE', '/api/estimate/' . $estimateId);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $this->assertNull($this->entityManager->getRepository(RideEstimate::class)->find($estimateId));
    }

    public function testUpdateEstimateRejectsNegative(): void
    {
        [, , $estimate] = $this->createRideWithEstimate();

        $this->client->request('POST', '/api/estimate/' . $estimate->getId(), [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['estimation' => -5]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }
}
