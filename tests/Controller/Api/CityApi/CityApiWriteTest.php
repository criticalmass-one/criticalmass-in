<?php declare(strict_types=1);

namespace Tests\Controller\Api\CityApi;

use App\Entity\City;
use App\Entity\CitySlug;
use Tests\Controller\Api\AbstractApiControllerTestCase;

/**
 * Tests der schreibenden City-Endpunkte (Edit, Aktivieren/Deaktivieren).
 * Jede Testmethode läuft in einer zurückgerollten Transaktion, damit die
 * angelegte Test-Stadt die Fixtures nicht verunreinigt.
 */
class CityApiWriteTest extends AbstractApiControllerTestCase
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
        $slug = 'api-write-' . substr(md5(uniqid('', true)), 0, 12);

        $city = new City();
        $city->setCity('Schreibstadt');
        $city->setTitle('Critical Mass Schreibstadt');
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

    public function testUpdateCityChangesTitle(): void
    {
        $city = $this->createCity();
        $slug = $city->getMainSlugString();

        $this->client->request('POST', '/api/' . $slug, [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['title' => 'Geänderter Titel']));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $cityId = $city->getId();
        $this->entityManager->clear();
        $updated = $this->entityManager->getRepository(City::class)->find($cityId);
        $this->assertSame('Geänderter Titel', $updated?->getTitle());
    }

    public function testUpdateCityRejectsBlankName(): void
    {
        $city = $this->createCity();
        $slug = $city->getMainSlugString();

        $this->client->request('POST', '/api/' . $slug, [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['name' => '']));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testDeactivateCity(): void
    {
        $city = $this->createCity();
        $slug = $city->getMainSlugString();

        $this->client->request('POST', '/api/' . $slug . '/enabled', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['enabled' => false]));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $cityId = $city->getId();
        $this->entityManager->clear();
        $reloaded = $this->entityManager->getRepository(City::class)->find($cityId);
        $this->assertFalse($reloaded?->isEnabled());
    }

    public function testSetCityEnabledRejectsMissingFlag(): void
    {
        $city = $this->createCity();
        $slug = $city->getMainSlugString();

        $this->client->request('POST', '/api/' . $slug . '/enabled', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['foo' => 'bar']));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }
}
