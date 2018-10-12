<?php declare(strict_types=1);

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontpageControllerTest extends WebTestCase
{
    public function testFrontpage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}