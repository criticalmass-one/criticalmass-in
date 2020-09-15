<?php declare(strict_types=1);

namespace Tests\Controller\Blog;

use Tests\Controller\AbstractControllerTest;

class BlogPostCommentTest extends AbstractControllerTest
{
    public function testBlogPostCommentFormInvisibleForNonLoggedInUsers(): void
    {
        $client = static::createClient();

        $client->request('GET', '/blog/testeintrag');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorNotExists('#modal-add-post');
    }

    public function testBlogPostCommentFormVisibleForLoggedInUsers(): void
    {
        $client = static::createClient();
        $client = $this->loginViaForm($client, 'maltehuebner', '123456');

        $client->request('GET', '/blog/testeintrag');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorExists('#modal-add-post');
    }

    public function testWriteBlogPostComment(): void
    {
        $client = static::createClient();
        $client = $this->loginViaForm($client, 'maltehuebner', '123456');

        $crawler = $client->request('GET', '/blog/testeintrag');

        $form = $crawler->filter('#modal-add-post form')->form();

        $messageText = sprintf('Testnachricht %s', uniqid('', true));

        $form->setValues([
            'post[message]' => $messageText,
        ]);

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('.post', $messageText);
    }
}