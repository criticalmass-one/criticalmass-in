<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use App\Entity\Ride;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTest;

class CityQueryTest extends AbstractApiControllerTest
{
    #[TestDox('Querying for Hamburg will only return Hamburg rides.')]
    public function testRideListWithCityQueryForHamburg(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?citySlug=hamburg');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        /** @var Ride $actualRide */
        foreach ($actualRideList as $actualRide) {
            $this->assertStringContainsString('Hamburg', $actualRide->getTitle());
        }
    }

    #[TestDox('Querying for Berlin will only return Berlin rides.')]
    public function testRideListWithCityQueryForBerlin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?citySlug=berlin');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        /** @var Ride $actualRide */
        foreach ($actualRideList as $actualRide) {
            $this->assertStringContainsString('Berlin', $actualRide->getTitle());
        }
    }

    #[TestDox('Expect an error when providing a non existent slug.')]
    public function testRideListWithCityQueryForNonExistentCity(): void
    {
        $client = static::createClient();
        $client->catchExceptions(false);

        // Non-existent city slug causes an exception in CityQuery
        $this->expectException(\Error::class);
        $client->request('GET', '/api/ride?citySlug=foobarcity');
    }
}
