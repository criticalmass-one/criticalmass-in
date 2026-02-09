<?php declare(strict_types=1);

namespace Tests\Controller\Api;

use App\DataFixtures\PhotoFixtures;
use App\Entity\Photo;

class PhotoControllerTest extends AbstractApiControllerTestCase
{
    public function testShowReturnsPhotoDetails(): void
    {
        /** @var Photo $photo */
        $photo = $this->entityManager
            ->getRepository(Photo::class)
            ->findOneBy(['imageName' => 'hamburg_ride_001.jpg']);

        $this->assertNotNull($photo, 'Fixture photo should exist');

        $this->client->request('GET', sprintf('/api/photo/%d', $photo->getId()));

        $this->assertResponseStatusCode(200);

        $response = $this->getJsonResponse();

        $this->assertEquals($photo->getId(), $response['id']);
        $this->assertEquals($photo->getLatitude(), $response['latitude']);
        $this->assertEquals($photo->getLongitude(), $response['longitude']);
        $this->assertEquals($photo->getDescription(), $response['description']);
        $this->assertEquals($photo->getLocation(), $response['location']);
        $this->assertEquals($photo->getImageName(), $response['imageName']);
        $this->assertArrayHasKey('views', $response);
        $this->assertArrayHasKey('exifCreationDate', $response);
        $this->assertArrayHasKey('creationDateTime', $response);
    }

    public function testShowReturns404ForNonExistentPhoto(): void
    {
        $this->client->request('GET', '/api/photo/999999');

        $this->assertResponseStatusCode(404);
    }

    public function testShowReturns404ForDeletedPhoto(): void
    {
        /** @var Photo $photo */
        $photo = $this->entityManager
            ->getRepository(Photo::class)
            ->findOneBy(['imageName' => 'hamburg_ride_deleted.jpg']);

        $this->assertNotNull($photo, 'Deleted fixture photo should exist');
        $this->assertTrue($photo->isDeleted(), 'Photo should be marked as deleted');

        $this->client->request('GET', sprintf('/api/photo/%d', $photo->getId()));

        $this->assertResponseStatusCode(404);
    }

    public function testShowReturns404ForDisabledPhoto(): void
    {
        /** @var Photo $photo */
        $photo = $this->entityManager
            ->getRepository(Photo::class)
            ->findOneBy(['imageName' => 'berlin_ride_disabled.jpg']);

        $this->assertNotNull($photo, 'Disabled fixture photo should exist');
        $this->assertFalse($photo->isEnabled(), 'Photo should be disabled');

        $this->client->request('GET', sprintf('/api/photo/%d', $photo->getId()));

        $this->assertResponseStatusCode(404);
    }

    public function testShowResponseContainsExifData(): void
    {
        /** @var Photo $photo */
        $photo = $this->entityManager
            ->getRepository(Photo::class)
            ->findOneBy(['imageName' => 'hamburg_ride_001.jpg']);

        $this->client->request('GET', sprintf('/api/photo/%d', $photo->getId()));

        $this->assertResponseStatusCode(200);

        $response = $this->getJsonResponse();

        $this->assertArrayHasKey('exifExposure', $response);
        $this->assertArrayHasKey('exifAperture', $response);
        $this->assertArrayHasKey('exifIso', $response);
        $this->assertArrayHasKey('exifFocalLength', $response);
        $this->assertArrayHasKey('exifCamera', $response);
        $this->assertArrayHasKey('exifCreationDate', $response);
    }

    public function testShowResponseContainsImageMetadata(): void
    {
        /** @var Photo $photo */
        $photo = $this->entityManager
            ->getRepository(Photo::class)
            ->findOneBy(['imageName' => 'hamburg_ride_001.jpg']);

        $this->client->request('GET', sprintf('/api/photo/%d', $photo->getId()));

        $this->assertResponseStatusCode(200);

        $response = $this->getJsonResponse();

        $this->assertArrayHasKey('imageName', $response);
        $this->assertArrayHasKey('imageSize', $response);
        $this->assertArrayHasKey('imageMimeType', $response);
    }
}
