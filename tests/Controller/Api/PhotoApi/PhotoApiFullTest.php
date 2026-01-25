<?php declare(strict_types=1);

namespace Tests\Controller\Api\PhotoApi;

use App\Entity\Photo;
use App\Entity\Ride;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class PhotoApiFullTest extends AbstractApiControllerTestCase
{
    public function testListPhotos(): void
    {
        $this->client->request('GET', '/api/photo');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response[0]);
    }

    public function testListPhotosWithSize(): void
    {
        $this->client->request('GET', '/api/photo', ['size' => 2]);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertLessThanOrEqual(2, count($response));
    }

    public function testListPhotosForCity(): void
    {
        $this->client->request('GET', '/api/photo', ['citySlug' => 'hamburg']);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
    }

    public function testListPhotosForRide(): void
    {
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();
        $this->assertNotEmpty($rides, 'No rides found in database');

        /** @var Ride $ride */
        foreach ($rides as $ride) {
            $photos = $this->entityManager->getRepository(Photo::class)->findPhotosByRide($ride);
            if (count($photos) > 0) {
                $dateString = $ride->getDateTime()->format('Y-m-d');
                $citySlug = $ride->getCity()->getMainSlugString();

                $this->client->request('GET', sprintf('/api/%s/%s/listPhotos', $citySlug, $dateString));

                $this->assertResponseIsSuccessful();

                $response = $this->getJsonResponse();

                $this->assertIsArray($response);
                $this->assertNotEmpty($response);
                return;
            }
        }

        $this->markTestSkipped('No rides with photos found');
    }

    public function testListPhotosWithRadiusQuery(): void
    {
        $this->client->request('GET', '/api/photo', [
            'centerLatitude' => 53.5611,
            'centerLongitude' => 9.9895,
            'radius' => 50,
        ]);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
    }

    public function testPhotoHasExpectedProperties(): void
    {
        $this->client->request('GET', '/api/photo', ['size' => 1]);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);

        $photo = $response[0];

        $this->assertArrayHasKey('id', $photo);
        $this->assertNotNull($photo['id']);
        $this->assertArrayHasKey('latitude', $photo);
        $this->assertNotNull($photo['latitude']);
        $this->assertArrayHasKey('longitude', $photo);
        $this->assertNotNull($photo['longitude']);
    }

    public function testListPhotosOrderByExifCreationDate(): void
    {
        $this->client->request('GET', '/api/photo', [
            'orderBy' => 'exifCreationDate',
            'orderDirection' => 'desc',
            'size' => 10,
        ]);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
    }
}
