<?php declare(strict_types=1);

namespace Tests\Controller\Api\LocationApi;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Location;
use Tests\Controller\Api\AbstractApiControllerTestCase;

/**
 * Tests der schreibenden Location-Endpunkte (create/update/delete).
 * Transaktions-isoliert.
 */
class LocationApiWriteTest extends AbstractApiControllerTestCase
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

    private function createCity(): City
    {
        $slug = 'location-api-' . substr(md5(uniqid('', true)), 0, 12);

        $city = new City();
        $city->setCity('Locationstadt');
        $city->setTitle('Critical Mass Locationstadt');
        $city->setCreatedAt(new \DateTime());
        $this->entityManager->persist($city);

        $citySlug = new CitySlug();
        $citySlug->setSlug($slug);
        $citySlug->setCity($city);
        $this->entityManager->persist($citySlug);
        $city->setMainSlug($citySlug);
        $this->entityManager->flush();

        return $city;
    }

    private function addLocation(City $city, string $slug = 'treffpunkt'): Location
    {
        $location = new Location();
        $location->setCity($city);
        $location->setTitle('Treffpunkt');
        $location->setSlug($slug);
        $this->entityManager->persist($location);
        $this->entityManager->flush();

        return $location;
    }

    public function testCreateLocation(): void
    {
        $city = $this->createCity();

        $this->client->request('PUT', '/api/' . $city->getMainSlugString() . '/location', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['title' => 'Rathausmarkt', 'latitude' => 53.55, 'longitude' => 9.99]));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertCount(1, $this->entityManager->getRepository(Location::class)->findBy(['city' => $city]));
    }

    public function testCreateLocationRejectsMissingTitle(): void
    {
        $city = $this->createCity();

        $this->client->request('PUT', '/api/' . $city->getMainSlugString() . '/location', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['latitude' => 53.55]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdateLocationChangesTitle(): void
    {
        $city = $this->createCity();
        $location = $this->addLocation($city);
        $locationId = $location->getId();

        $this->client->request('POST', '/api/' . $city->getMainSlugString() . '/location/treffpunkt', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['title' => 'Geänderter Treffpunkt']));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $updated = $this->entityManager->getRepository(Location::class)->find($locationId);
        $this->assertSame('Geänderter Treffpunkt', $updated?->getTitle());
    }

    public function testDeleteLocation(): void
    {
        $city = $this->createCity();
        $location = $this->addLocation($city);
        $locationId = $location->getId();

        $this->client->request('DELETE', '/api/' . $city->getMainSlugString() . '/location/treffpunkt');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $this->assertNull($this->entityManager->getRepository(Location::class)->find($locationId));
    }

    public function testDeleteUnknownLocationReturns404(): void
    {
        $city = $this->createCity();

        $this->client->request('DELETE', '/api/' . $city->getMainSlugString() . '/location/gibtsnicht');

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}
