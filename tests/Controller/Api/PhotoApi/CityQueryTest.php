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

    #[TestDox('Expect an error when providing a non existent slug.')]
    public function testPhotoListWithCityQueryForNonExistentCity(): void
    {
        $this->client->catchExceptions(false);

        // Non-existent city slug causes an exception in CityQuery
        // when trying to access getCity() on null result
        $this->expectException(\Error::class);
        $this->client->request('GET', '/api/photo?citySlug=foobarcity');
    }
}
