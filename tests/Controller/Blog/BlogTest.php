<?php declare(strict_types=1);

namespace Tests\Controller\Blog;

use Tests\Controller\AbstractControllerTest;

class BlogTest extends AbstractControllerTest
{
    public function testBlogVisible(): void
    {
        $client = static::createClient();

        $client->request('GET', '/blog/');

        $this->assertEquals(301, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('html h2', 'Testeintrag');
        $this->assertSelectorTextContains('html h2', 'Testeintrag ohne Intro');
    }
}