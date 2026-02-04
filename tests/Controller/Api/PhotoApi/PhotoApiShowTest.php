<?php declare(strict_types=1);

namespace Tests\Controller\Api\PhotoApi;

use App\Entity\Photo;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class PhotoApiShowTest extends AbstractApiControllerTestCase
{
    public function testShowPhoto(): void
    {
        $photo = $this->getEnabledPhoto();

        $this->client->request('GET', sprintf('/api/photo/%d', $photo->getId()));

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertEquals($photo->getId(), $response['id']);
    }

    public function testShowPhotoReturnsImageName(): void
    {
        $photo = $this->getEnabledPhoto();

        $this->client->request('GET', sprintf('/api/photo/%d', $photo->getId()));

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertArrayHasKey('image_name', $response);
        $this->assertNotNull($response['image_name']);
        $this->assertEquals($photo->getImageName(), $response['image_name']);
    }

    public function testShowPhotoReturnsExpectedProperties(): void
    {
        $photo = $this->getEnabledPhoto();

        $this->client->request('GET', sprintf('/api/photo/%d', $photo->getId()));

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('latitude', $response);
        $this->assertArrayHasKey('longitude', $response);
        $this->assertArrayHasKey('description', $response);
        $this->assertArrayHasKey('views', $response);
        $this->assertArrayHasKey('image_name', $response);
        $this->assertArrayHasKey('location', $response);
    }

    public function testShowDeletedPhotoReturns404(): void
    {
        $deletedPhoto = $this->entityManager->getRepository(Photo::class)
            ->findOneBy(['deleted' => true]);

        if (!$deletedPhoto) {
            $this->markTestSkipped('No deleted photo found in database');
        }

        $this->client->request('GET', sprintf('/api/photo/%d', $deletedPhoto->getId()));

        $this->assertResponseStatusCode(404);
    }

    public function testShowDisabledPhotoReturns404(): void
    {
        $disabledPhoto = $this->entityManager->getRepository(Photo::class)
            ->findOneBy(['enabled' => false]);

        if (!$disabledPhoto) {
            $this->markTestSkipped('No disabled photo found in database');
        }

        $this->client->request('GET', sprintf('/api/photo/%d', $disabledPhoto->getId()));

        $this->assertResponseStatusCode(404);
    }

    public function testShowNonExistentPhotoReturns404(): void
    {
        $this->client->request('GET', '/api/photo/999999');

        $this->assertResponseStatusCode(404);
    }

    private function getEnabledPhoto(): Photo
    {
        $photo = $this->entityManager->getRepository(Photo::class)
            ->findOneBy(['enabled' => true, 'deleted' => false]);

        $this->assertNotNull($photo, 'No enabled photo found in database');

        return $photo;
    }
}
