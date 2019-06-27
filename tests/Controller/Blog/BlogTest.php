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

        $this->assertSelectorTextContains('article[data-blog-post-slug="testeintrag"] h2', 'Testeintrag');
    }

    public function testTwoBlogPostsAreVisible(): void
    {
        $client = static::createClient();

        $client->request('GET', '/blog/');

        $this->assertEquals(301, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('article[data-blog-post-slug="testeintrag"] h2.blog-post-title', 'Testeintrag');
        $this->assertSelectorTextContains('article[data-blog-post-slug="testeintrag"] p.blog-post-intro', 'Testintro');
        $this->assertSelectorNotExists('article[data-blog-post-slug="testeintrag"] div.blog-post-content');

        $this->assertSelectorNotExists('article[data-blog-post-slug="unsichtbarer-testeintrag"]');

        $this->assertSelectorTextContains('article[data-blog-post-slug="testeintrag-ohne-intro"] h2.blog-post-title', 'Testeintrag ohne Intro');
        $this->assertSelectorNotExists('article[data-blog-post-slug="testeintrag-ohne-intro"] p.blog-post-intro');
        $this->assertSelectorTextContains('article[data-blog-post-slug="testeintrag-ohne-intro"] div.blog-post-content', 'Testtext ohne Intro');
    }
}