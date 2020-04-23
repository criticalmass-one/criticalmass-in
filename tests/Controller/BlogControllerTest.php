<?php declare(strict_types=1);

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogControllerTest extends WebTestCase
{
    public function isBlogVisible(): void
    {
        $client = static::createClient();

        $client->request('GET', '/blog');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}