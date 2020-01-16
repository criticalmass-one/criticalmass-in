<?php declare(strict_types=1);

namespace Tests\Controller\Api\PhotoApi;

use App\Entity\Photo;
use Tests\Controller\Api\AbstractApiControllerTest;

class CityQueryTest extends AbstractApiControllerTest
{
    /**
     * @testdox Querying for Hamburg will only return Hamburg photos.
     */
    public function testPhotoListWithCityQueryForHamburg(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo?citySlug=hamburg');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        /** @var Photo $actualPhoto */
        foreach ($actualPhotoList as $actualPhoto) {
            //$this->assertEquals('Hamburg', $actualPhoto->getCity()->getCity());
            $this->assertContains('Hamburg', $actualPhoto->getCity()->getCity());
        }
    }

    /**
     * @testdox Querying for London will only return London photos.
     */
    public function testPhotoListWithCityQueryForLondon(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo?citySlug=london');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        /** @var Photo $actualPhoto */
        foreach ($actualPhotoList as $actualPhoto) {
            //$this->assertEquals('London', $actualPhoto->getCity()->getCity());
            $this->assertContains('London', $actualPhoto->getCity()->getCity());
        }
    }

    /**
     * @testdox Expect 10 random photos when providing an non existent slug.
     */
    public function testPhotoListWithCityQueryForNonExistentCity(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo?citySlug=foobarcity');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        $this->assertCount(10, $actualPhotoList);
    }
}
