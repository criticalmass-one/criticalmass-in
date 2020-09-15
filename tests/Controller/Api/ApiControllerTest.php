<?php declare(strict_types=1);

namespace Tests\Controller\Api;

class ApiControllerTest extends AbstractApiControllerTest
{
    public function testApiDocVisible(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/doc');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h1', 'API documentation');
    }
}
