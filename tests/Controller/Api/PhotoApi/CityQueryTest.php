<?php declare(strict_types=1);

namespace Tests\Controller\Api\PhotoApi;

use App\Entity\Photo;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class CityQueryTest extends AbstractApiControllerTestCase
{
    #[TestDox('Querying for Hamburg will only return Hamburg photos.')]
    public function testPhotoListWithCityQueryForHamburg(): void
    {

        $this->client->request('GET', '/api/photo?citySlug=hamburg');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($this->client->getResponse()->getContent(), Photo::class);

        /** @var Photo $actualPhoto */
        foreach ($actualPhotoList as $actualPhoto) {
            $this->assertStringContainsString('Hamburg', $actualPhoto->getCity()->getCity());
        }
    }

    #[TestDox('Querying for Berlin will only return Berlin photos.')]
    public function testPhotoListWithCityQueryForBerlin(): void
    {

        $this->client->request('GET', '/api/photo?citySlug=berlin');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($this->client->getResponse()->getContent(), Photo::class);

        /** @var Photo $actualPhoto */
        foreach ($actualPhotoList as $actualPhoto) {
            $this->assertStringContainsString('Berlin', $actualPhoto->getCity()->getCity());
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
