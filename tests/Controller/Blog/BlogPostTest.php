<?php declare(strict_types=1);

namespace Tests\Controller\Blog;

use Tests\Controller\AbstractControllerTest;

class BlogPostTest extends AbstractControllerTest
{
    public function testBlogPostVisible(): void
    {
        $client = static::createClient();

        $client->request('GET', '/blog/testeintrag');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('html h2', 'Testeintrag');
    }

    public function testBlogPostInvisible(): void
    {
        $client = static::createClient();

        $client->request('GET', '/blog/unsichtbarer-testeintrag');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testBlogPostPageStructureWithIntro(): void
    {
        $client = static::createClient();

        $client->request('GET', '/blog/testeintrag');

        $this->assertSelectorTextContains('h2.blog-post-title', 'Testeintrag');
        $this->assertSelectorTextContains('p.blog-post-intro', 'Testintro');
        $this->assertSelectorTextContains('div.blog-post-content', 'Testtext');
    }

    public function testBlogPostPageStructureWithoutIntro(): void
    {
        $client = static::createClient();

        $client->request('GET', '/blog/testeintrag-ohne-intro');

        $this->assertSelectorTextContains('h2.blog-post-title', 'Testeintrag ohne Intro');
        $this->assertSelectorNotExists('p.blog-post-intro');
        $this->assertSelectorTextContains('div.blog-post-content', 'Testtext ohne Intro');
    }
}