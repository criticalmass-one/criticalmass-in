<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use App\Entity\Ride;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class CityQueryTest extends AbstractApiControllerTestCase
{
    #[TestDox('Querying for Hamburg will only return Hamburg rides.')]
    public function testRideListWithCityQueryForHamburg(): void
    {

        $this->client->request('GET', '/api/ride?citySlug=hamburg');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $actualRideList = $this->deserializeEntityList($this->client->getResponse()->getContent(), Ride::class);

        $this->assertNotEmpty($actualRideList, 'Should return Hamburg rides');

        // Verify rides are in Hamburg area by coordinates (around 53.55, 10.0)
        /** @var Ride $actualRide */
        foreach ($actualRideList as $actualRide) {
            // Hamburg coordinates: approx 53.55 N, 10.0 E
            $this->assertGreaterThan(53.4, $actualRide->getLatitude());
            $this->assertLessThan(53.7, $actualRide->getLatitude());
            $this->assertGreaterThan(9.8, $actualRide->getLongitude());
            $this->assertLessThan(10.2, $actualRide->getLongitude());
        }
    }

    #[TestDox('Querying for Berlin will only return Berlin rides.')]
    public function testRideListWithCityQueryForBerlin(): void
    {

        $this->client->request('GET', '/api/ride?citySlug=berlin');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $actualRideList = $this->deserializeEntityList($this->client->getResponse()->getContent(), Ride::class);

        $this->assertNotEmpty($actualRideList, 'Should return Berlin rides');

        // Verify rides are in Berlin area by coordinates (around 52.5, 13.4)
        /** @var Ride $actualRide */
        foreach ($actualRideList as $actualRide) {
            // Berlin coordinates: approx 52.5 N, 13.4 E
            $this->assertGreaterThan(52.3, $actualRide->getLatitude());
            $this->assertLessThan(52.7, $actualRide->getLatitude());
            $this->assertGreaterThan(13.2, $actualRide->getLongitude());
            $this->assertLessThan(13.6, $actualRide->getLongitude());
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
