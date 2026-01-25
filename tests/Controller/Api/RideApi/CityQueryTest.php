<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class CityQueryTest extends AbstractApiControllerTestCase
{
    #[TestDox('Querying for Hamburg will only return Hamburg rides.')]
    public function testRideListWithCityQueryForHamburg(): void
    {
        $this->client->request('GET', '/api/ride?citySlug=hamburg');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response, 'Should return Hamburg rides');

        // Verify rides are in Hamburg area by coordinates (around 53.55, 10.0)
        foreach ($response as $ride) {
            // Hamburg coordinates: approx 53.55 N, 10.0 E
            $this->assertGreaterThan(53.4, $ride['latitude']);
            $this->assertLessThan(53.7, $ride['latitude']);
            $this->assertGreaterThan(9.8, $ride['longitude']);
            $this->assertLessThan(10.2, $ride['longitude']);
        }
    }

    #[TestDox('Querying for Berlin will only return Berlin rides.')]
    public function testRideListWithCityQueryForBerlin(): void
    {
        $this->client->request('GET', '/api/ride?citySlug=berlin');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response, 'Should return Berlin rides');

        // Verify rides are in Berlin area by coordinates (around 52.5, 13.4)
        foreach ($response as $ride) {
            // Berlin coordinates: approx 52.5 N, 13.4 E
            $this->assertGreaterThan(52.3, $ride['latitude']);
            $this->assertLessThan(52.7, $ride['latitude']);
            $this->assertGreaterThan(13.2, $ride['longitude']);
            $this->assertLessThan(13.6, $ride['longitude']);
        }
    }

    #[TestDox('Expect an error when providing a non existent slug.')]
    public function testRideListWithCityQueryForNonExistentCity(): void
    {
        $this->client->catchExceptions(false);

        // Non-existent city slug causes an exception in CityQuery
        $this->expectException(\Error::class);
        $this->client->request('GET', '/api/ride?citySlug=foobarcity');
    }
}
