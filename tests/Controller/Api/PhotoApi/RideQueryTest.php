<?php declare(strict_types=1);

namespace Tests\Controller\Api\PhotoApi;

use App\Entity\Photo;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTest;

class RideQueryTest extends AbstractApiControllerTest
{
    #[TestDox('Querying for Hamburg with past ride date will only return Hamburg photos.')]
    public function testPhotoListWithRideQueryForHamburg(): void
    {
        $client = static::createClient();

        $rideDate = (new \DateTime('-1 month last friday'))->format('Y-m-d');
        $client->request('GET', '/api/photo?citySlug=hamburg&rideIdentifier=' . $rideDate);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        /** @var Photo $actualPhoto */
        foreach ($actualPhotoList as $actualPhoto) {
            $this->assertStringContainsString('Hamburg', $actualPhoto->getCity()->getCity());
        }
    }

    #[TestDox('Querying for Berlin with past ride date will only return Berlin photos.')]
    public function testPhotoListWithRideQueryForBerlin(): void
    {
        $client = static::createClient();

        $rideDate = (new \DateTime('-1 month last friday'))->format('Y-m-d');
        $client->request('GET', '/api/photo?citySlug=berlin&rideIdentifier=' . $rideDate);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        /** @var Photo $actualPhoto */
        foreach ($actualPhotoList as $actualPhoto) {
            $this->assertStringContainsString('Berlin', $actualPhoto->getCity()->getCity());
        }
    }

    #[TestDox('Expect 10 random photos when providing an non existent slug for city and ride.')]
    public function testPhotoListWithCityQueryForNonExistentCity(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo?citySlug=foobarcity&rideIdentifier=1245');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        $this->assertCount(10, $actualPhotoList);
    }
}
