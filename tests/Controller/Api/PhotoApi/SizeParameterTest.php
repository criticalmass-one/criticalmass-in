<?php declare(strict_types=1);

namespace Tests\Controller\Api\PhotoApi;

use App\Entity\Photo;
use Tests\Controller\Api\AbstractApiControllerTest;

class SizeParameterTest extends AbstractApiControllerTest
{
    /**
     * @testdox Calling api without size parameter delivers 10 results.
     */
    public function testPhotoListWithBoundingSizeParameter(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        $this->assertCount(10, $actualPhotoList);
    }

    /**
     * @testdox Request 5 results.
     */
    public function testPhotoListWith5Results(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo?size=5');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        $this->assertCount(5, $actualPhotoList);
    }

    /**
     * @testdox Request 15 photos.
     */
    public function testPhotoListWith15Results(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo?size=15');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        $this->assertCount(15, $actualPhotoList);
    }

    /**
     * @testdox Requesting 1 photo will result in a list with 1 photo.
     */
    public function testPhotoListWith1Result(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo?size=1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        $this->assertCount(1, $actualPhotoList);
    }

    /**
     * @testdox Calling size=0 will default to 10 results.
     */
    public function testPhotoListWithSize0Returning10Results(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo?size=0');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        $this->assertCount(10, $actualPhotoList);
    }

    /**
     * @testdox Calling size=-1 will default to 10 results.
     */
    public function testPhotoListWithNegativeParameter(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo?size=-1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        $this->assertCount(10, $actualPhotoList);
    }

    /**
     * @testdox Using strings as parameter value will default to 10 results.
     */
    public function testPhotoListWithInvalidParameter(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/photo?size=abc');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualPhotoList = $this->deserializeEntityList($client->getResponse()->getContent(), Photo::class);

        $this->assertCount(10, $actualPhotoList);
    }
}
