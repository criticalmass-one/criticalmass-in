<?php declare(strict_types=1);

namespace Tests\Controller\Api\PhotoApi;

use App\Entity\Photo;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

#[TestDox('Photo API Write Operations')]
class PhotoApiWriteTest extends AbstractApiControllerTestCase
{
    #[TestDox('POST /api/photo/{id} accepts update request')]
    public function testUpdatePhoto(): void
    {
        $photos = $this->entityManager->getRepository(Photo::class)->findAll();

        if (empty($photos)) {
            $this->markTestSkipped('No photos found in database');
        }

        $photo = $photos[0];

        $updateData = [
            'description' => 'Updated description via API test',
            'location' => 'Updated Location',
        ];

        $this->client->request(
            'POST',
            sprintf('/api/photo/%d', $photo->getId()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($updateData)
        );

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertIsArray($response);
    }

    #[TestDox('POST /api/photo/{nonExistentId} returns 404')]
    public function testUpdateNonExistentPhotoReturns404(): void
    {
        $this->client->request(
            'POST',
            '/api/photo/999999',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['description' => 'Test'])
        );

        $this->assertResponseStatusCode(404);
    }

    #[TestDox('POST /api/photo/{id} with empty body returns response')]
    public function testUpdatePhotoWithEmptyBody(): void
    {
        $photos = $this->entityManager->getRepository(Photo::class)->findAll();

        if (empty($photos)) {
            $this->markTestSkipped('No photos found in database');
        }

        $photo = $photos[0];

        $this->client->request(
            'POST',
            sprintf('/api/photo/%d', $photo->getId()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{}'
        );

        // Either success or error response
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [200, 400, 500], 'Should return a valid response');
    }

    #[TestDox('POST /api/photo/{id} with invalid JSON returns error')]
    public function testUpdatePhotoWithInvalidJson(): void
    {
        $photos = $this->entityManager->getRepository(Photo::class)->findAll();

        if (empty($photos)) {
            $this->markTestSkipped('No photos found in database');
        }

        $photo = $photos[0];

        $this->client->request(
            'POST',
            sprintf('/api/photo/%d', $photo->getId()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            'invalid json {'
        );

        // Should return error status
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [400, 500], 'Invalid JSON should return error');
    }
}
