<?php declare(strict_types=1);

namespace Tests\Controller\Api\PhotoApi;

use App\Entity\Photo;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTest;

class CityQueryTest extends AbstractApiControllerTest
{
    #[TestDox('Querying for Hamburg will only return Hamburg photos.')]
    public function testPhotoListWithCityQueryForHamburg(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo?citySlug=hamburg');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        /** @var Photo $actualPhoto */
        foreach ($actualPhotoList as $actualPhoto) {
            $this->assertStringContainsString('Hamburg', $actualPhoto->getCity()->getCity());
        }
    }

    #[TestDox('Querying for Berlin will only return Berlin photos.')]
    public function testPhotoListWithCityQueryForBerlin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo?citySlug=berlin');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        /** @var Photo $actualPhoto */
        foreach ($actualPhotoList as $actualPhoto) {
            $this->assertStringContainsString('Berlin', $actualPhoto->getCity()->getCity());
        }
    }

    #[TestDox('Expect 10 random photos when providing an non existent slug.')]
    public function testPhotoListWithCityQueryForNonExistentCity(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo?citySlug=foobarcity');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        $this->assertCount(10, $actualPhotoList);
    }
}
