<?php declare(strict_types=1);

namespace Tests\Controller\Api\PhotoApi;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Photo;
use App\Entity\Ride;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

#[TestDox('Photo API Write Operations')]
class PhotoApiWriteTest extends AbstractApiControllerTestCase
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

    private function createPhoto(): Photo
    {
        $slug = 'photo-api-' . substr(md5(uniqid('', true)), 0, 12);

        $city = new City();
        $city->setCity('Fotostadt');
        $city->setTitle('Critical Mass Fotostadt');
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

        $photo = new Photo();
        $photo->setRide($ride);
        $photo->setCity($city);
        $photo->setImageName('test.jpg');
        $photo->setCreationDateTime(new \DateTime());
        $photo->setEnabled(true);
        $photo->setDeleted(false);
        $this->entityManager->persist($photo);

        $this->entityManager->flush();

        return $photo;
    }

    #[TestDox('POST /api/photo/{id} persists a description change')]
    public function testUpdatePhotoDescriptionPersists(): void
    {
        $photo = $this->createPhoto();
        $photoId = $photo->getId();

        $this->client->request(
            'POST',
            sprintf('/api/photo/%d', $photoId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['description' => 'Geänderte Beschreibung'])
        );

        $this->assertResponseIsSuccessful();

        $this->entityManager->clear();
        $updated = $this->entityManager->getRepository(Photo::class)->find($photoId);
        $this->assertSame('Geänderte Beschreibung', $updated?->getDescription());
    }

    #[TestDox('DELETE /api/photo/{id} soft-deletes the photo')]
    public function testDeletePhotoSoftDeletes(): void
    {
        $photo = $this->createPhoto();
        $photoId = $photo->getId();

        $this->client->request('DELETE', sprintf('/api/photo/%d', $photoId));

        $this->assertResponseIsSuccessful();

        $this->entityManager->clear();
        $reloaded = $this->entityManager->getRepository(Photo::class)->find($photoId);
        $this->assertTrue($reloaded?->isDeleted());
    }

    #[TestDox('POST /api/photo/{nonExistentId} returns 404')]
    public function testUpdateNonExistentPhotoReturns404(): void
    {
        $this->client->request(
            'POST',
            '/api/photo/999999999',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['description' => 'Test'])
        );

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    #[TestDox('POST /api/photo/{id} with empty body succeeds without changes')]
    public function testUpdatePhotoWithEmptyBody(): void
    {
        $photo = $this->createPhoto();

        $this->client->request(
            'POST',
            sprintf('/api/photo/%d', $photo->getId()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{}'
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    #[TestDox('POST /api/photo/{id} with invalid JSON returns 400')]
    public function testUpdatePhotoWithInvalidJson(): void
    {
        $photo = $this->createPhoto();

        $this->client->request(
            'POST',
            sprintf('/api/photo/%d', $photo->getId()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            'invalid json {'
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }
}
