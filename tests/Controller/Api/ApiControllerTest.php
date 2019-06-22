<?php declare(strict_types=1);

namespace Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    public function testFrontpageVisible(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/doc');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h1', 'API documentation');
    }

}