<?php declare(strict_types=1);

namespace Tests\Controller\Api;

class ApiControllerTest extends AbstractApiControllerTest
{
    public function testApiDocVisible(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/doc');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // Just verify the page loads - Swagger UI doesn't have a static h1
        $content = $client->getResponse()->getContent();
        $this->assertNotEmpty($content, 'API doc page should have content');
    }
}
