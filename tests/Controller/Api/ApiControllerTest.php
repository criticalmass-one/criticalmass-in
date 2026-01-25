<?php declare(strict_types=1);

namespace Tests\Controller\Api;

class ApiControllerTest extends AbstractApiControllerTestCase
{
    public function testApiDocVisible(): void
    {

        $this->client->request('GET', '/api/doc');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        // Just verify the page loads - Swagger UI doesn't have a static h1
        $content = $this->client->getResponse()->getContent();
        $this->assertNotEmpty($content, 'API doc page should have content');
    }
}
