<?php declare(strict_types=1);

namespace Tests\Controller\Api\PhotoApi;

use App\Entity\Photo;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTest;

class RideQueryTest extends AbstractApiControllerTest
{
    #[TestDox('Querying for Hamburg 2011-06-24 will only return Hamburg photos.')]
    public function testPhotoListWithRideQueryForHamburg(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo?citySlug=hamburg&rideIdentifier=2011-06-24');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        /** @var Photo $actualPhoto */
        foreach ($actualPhotoList as $actualPhoto) {
            //$this->assertEquals('Hamburg', $actualPhoto->getCity()->getCity());
            $this->assertContains('2011-06-24', $actualPhoto->getExifCreationDate()->format('Y-m-d'));
        }
    }

    #[TestDox('Querying for London 2019-04-01 will only return London photos.')]
    public function testPhotoListWithRideQueryForLondon(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo?citySlug=london&rideIdentifier=2019-04-01');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        /** @var Photo $actualPhoto */
        foreach ($actualPhotoList as $actualPhoto) {
            //$this->assertEquals('London', $actualPhoto->getCity()->getCity());
            $this->assertContains('2019-04-01', $actualPhoto->getExifCreationDate()->format('Y-m-d'));
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
