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
            //$this->assertEquals('Hamburg', $actualRide->getCity()->getCity());
            $this->assertContains('Hamburg', $actualRide->getTitle());
        }
    }

    #[TestDox('Querying for London will only return London rides.')]
    public function testRideListWithCityQueryForLondon(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?citySlug=london');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        /** @var Ride $actualRide */
        foreach ($actualRideList as $actualRide) {
            //$this->assertEquals('London', $actualRide->getCity()->getCity());
            $this->assertContains('London', $actualRide->getTitle());
        }
    }

    #[TestDox('Expect 10 random cities when providing an non existent slug.')]
    public function testRideListWithCityQueryForNonExistentCity(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?citySlug=foobarcity');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);

        $this->assertCount(10, $actualRideList);
    }
}
