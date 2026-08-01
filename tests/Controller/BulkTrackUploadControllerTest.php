<?php declare(strict_types=1);

namespace Tests\Controller;

class BulkTrackUploadControllerTest extends AbstractControllerTestCase
{
    private const PAGE_URL = '/trackupload/bulk';

    public function testBulkUploadPageRedirectsToUnifiedUpload(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $client->request('GET', self::PAGE_URL);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertStringEndsWith('/upload', (string) $client->getResponse()->headers->get('Location'));
    }

    public function testBulkUploadPageRedirectsAnonymous(): void
    {
        $client = static::createClient();

        $client->request('GET', self::PAGE_URL);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
