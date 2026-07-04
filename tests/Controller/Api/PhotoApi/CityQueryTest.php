<?php declare(strict_types=1);

namespace Tests\Controller\Api\PhotoApi;

use App\Entity\Photo;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class CityQueryTest extends AbstractApiControllerTestCase
{
    #[TestDox('Querying for Hamburg will return photos.')]
    public function testPhotoListWithCityQueryForHamburg(): void
    {
        $this->client->request('GET', '/api/photo?citySlug=hamburg');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response = $this->getJsonResponse();

        // Verify we get an array of photos
        $this->assertIsArray($response);
        $this->assertNotEmpty($response, 'Should have photos for Hamburg');

        // Verify each item has expected photo properties
        foreach ($response as $photo) {
            $this->assertArrayHasKey('id', $photo);
        }
    }

    #[TestDox('Querying for Berlin will return photos.')]
    public function testPhotoListWithCityQueryForBerlin(): void
    {
        $this->client->request('GET', '/api/photo?citySlug=berlin');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response = $this->getJsonResponse();

        // Verify we get an array of photos
        $this->assertIsArray($response);
        $this->assertNotEmpty($response, 'Should have photos for Berlin');

        // Verify each item has expected photo properties
        foreach ($response as $photo) {
            $this->assertArrayHasKey('id', $photo);
        }
    }

    #[TestDox('Querying for a non existent slug returns 404 not found.')]
    public function testPhotoListWithCityQueryForNonExistentCity(): void
    {
        $this->client->request('GET', '/api/photo?citySlug=foobarcity');

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}
