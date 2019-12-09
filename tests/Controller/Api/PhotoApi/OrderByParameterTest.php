<?php declare(strict_types=1);

namespace Tests\Controller\Api\PhotoApi;

use App\Entity\Photo;
use Tests\Controller\Api\AbstractApiControllerTest;

class OrderByParameterTest extends AbstractApiControllerTest
{
    /**
     * @testdox Get 10 photos ordered by exifCreationDate ascending.
     */
    public function testPhotoListOrderByDateTimeAscending(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo?orderBy=exifCreationDate&orderDirection=ASC');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        $this->assertCount(10, $actualPhotoList);

        $minDateTime = null;

        /**
         * @var Photo $actualPhoto
         */
        foreach ($actualPhotoList as $actualPhoto) {
            if ($minDateTime) {
                $this->assertLessThanOrEqual($actualPhoto->getExifCreationDate(), $minDateTime);
            }

            $minDateTime = $actualPhoto->getExifCreationDate();
        }
    }

    /**
     * @testdox Get 10 photo ordered by exifCreationDate descending.
     */
    public function testPhotoListOrderByDateTimeDescending(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo?orderBy=exifCreationDate&orderDirection=DESC');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        $this->assertCount(10, $actualPhotoList);

        $maxDateTime = null;

        /**
         * @var Photo $actualPhoto
         */
        foreach ($actualPhotoList as $actualPhoto) {
            if ($maxDateTime) {
                $this->assertGreaterThanOrEqual($actualPhoto->getExifCreationDate(), $maxDateTime);
            }

            $maxDateTime = $actualPhoto->getExifCreationDate();
        }
    }

    /**
     * @testdox Providing invalid order direction will not break things.
     */
    public function testPhotoListOrderByTitleInvalidOrder(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo?orderBy=dateTime&orderDirection=FOO');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        $this->assertCount(10, $actualPhotoList);
    }

    /**
     * @testdox Providing invalid fields will not break api.
     */
    public function testPhotoListOrderByInvalidOrder(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo?orderBy=invalidField&orderDirection=DESC');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        $this->assertCount(10, $actualPhotoList);
    }
}
