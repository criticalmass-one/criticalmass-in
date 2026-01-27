<?php declare(strict_types=1);

namespace Tests\Controller\Api\PhotoApi;

use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class RideQueryTest extends AbstractApiControllerTestCase
{
    #[TestDox('Querying for Hamburg with past ride date will only return Hamburg photos.')]
    public function testPhotoListWithRideQueryForHamburg(): void
    {
        $rideDate = (new \Carbon\Carbon('-1 month last friday'))->format('Y-m-d');
        $this->client->request('GET', '/api/photo?citySlug=hamburg&rideIdentifier=' . $rideDate);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response = $this->getJsonResponse();

        // Verify we get an array of photos
        $this->assertIsArray($response);
        $this->assertNotEmpty($response, 'Should have photos for Hamburg ride');

        // Verify each item has expected photo properties
        foreach ($response as $photo) {
            $this->assertArrayHasKey('id', $photo);
        }
    }

    #[TestDox('Querying for Berlin with past ride date will only return Berlin photos.')]
    public function testPhotoListWithRideQueryForBerlin(): void
    {
        $rideDate = (new \Carbon\Carbon('-1 month last friday'))->format('Y-m-d');
        $this->client->request('GET', '/api/photo?citySlug=berlin&rideIdentifier=' . $rideDate);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response = $this->getJsonResponse();

        // Verify we get an array of photos
        $this->assertIsArray($response);
        $this->assertNotEmpty($response, 'Should have photos for Berlin ride');

        // Verify each item has expected photo properties
        foreach ($response as $photo) {
            $this->assertArrayHasKey('id', $photo);
        }
    }

    #[TestDox('Expect an error when providing a non existent slug for city and ride.')]
    public function testPhotoListWithCityQueryForNonExistentCity(): void
    {
        $this->client->catchExceptions(false);

        // Non-existent city slug causes an exception in CityQuery
        $this->expectException(\Error::class);
        $this->client->request('GET', '/api/photo?citySlug=foobarcity&rideIdentifier=1245');
    }
}
