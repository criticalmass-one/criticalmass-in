<?php declare(strict_types=1);

namespace Tests\Controller\Api\PhotoApi;

use App\Entity\Photo;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class RideQueryTest extends AbstractApiControllerTestCase
{
    #[TestDox('Querying for Hamburg with past ride date will only return Hamburg photos.')]
    public function testPhotoListWithRideQueryForHamburg(): void
    {

        $rideDate = (new \DateTime('-1 month last friday'))->format('Y-m-d');
        $this->client->request('GET', '/api/photo?citySlug=hamburg&rideIdentifier=' . $rideDate);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($this->client->getResponse()->getContent(), Photo::class);

        /** @var Photo $actualPhoto */
        foreach ($actualPhotoList as $actualPhoto) {
            $this->assertStringContainsString('Hamburg', $actualPhoto->getCity()->getCity());
        }
    }

    #[TestDox('Querying for Berlin with past ride date will only return Berlin photos.')]
    public function testPhotoListWithRideQueryForBerlin(): void
    {

        $rideDate = (new \DateTime('-1 month last friday'))->format('Y-m-d');
        $this->client->request('GET', '/api/photo?citySlug=berlin&rideIdentifier=' . $rideDate);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($this->client->getResponse()->getContent(), Photo::class);

        /** @var Photo $actualPhoto */
        foreach ($actualPhotoList as $actualPhoto) {
            $this->assertStringContainsString('Berlin', $actualPhoto->getCity()->getCity());
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
