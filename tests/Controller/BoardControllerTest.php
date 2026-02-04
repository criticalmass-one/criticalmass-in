<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\Thread;

class BoardControllerTest extends AbstractControllerTestCase
{
    public function testBoardOverviewAccessible(): void
    {
        $client = static::createClient();

        $client->request('GET', '/boards/overview');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testThreadListAccessible(): void
    {
        $client = static::createClient();

        $client->request('GET', '/boards/general');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAddThreadRedirectsWithoutLogin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/boards/general/addthread');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testAddThreadFormAccessible(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $client->request('GET', '/boards/general/addthread');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreateThreadRedirects(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $crawler = $client->request('GET', '/boards/general/addthread');

        $form = $crawler->filter('button[type="submit"]')->form();
        $form['form[title]'] = 'Neues Testthema';
        $form['form[message]'] = 'Das ist die erste Nachricht im Testthema.';

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testCreatedThreadContainsTitleAndMessage(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $crawler = $client->request('GET', '/boards/general/addthread');

        $form = $crawler->filter('button[type="submit"]')->form();
        $form['form[title]'] = 'Sichtbares Testthema';
        $form['form[message]'] = 'Diese Nachricht muss sichtbar sein.';

        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertStringContainsString('Sichtbares Testthema', $crawler->text());
        $this->assertStringContainsString('Diese Nachricht muss sichtbar sein.', $crawler->text());
    }

    public function testReplyToThreadRedirects(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        // Create a thread first
        $crawler = $client->request('GET', '/boards/general/addthread');
        $form = $crawler->filter('button[type="submit"]')->form();
        $form['form[title]'] = 'Thread fuer Antworttest';
        $form['form[message]'] = 'Erste Nachricht.';
        $client->submit($form);

        $em = static::getContainer()->get('doctrine')->getManager();
        $thread = $em->getRepository(Thread::class)->findOneBy(['title' => 'Thread fuer Antworttest']);
        $this->assertNotNull($thread, 'Thread should exist after creation');

        // Get the reply form
        $crawler = $client->request('GET', sprintf('/post/write/thread/%s', $thread->getSlug()));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->filter('button[type="submit"]')->form();
        $form['post[message]'] = 'Das ist eine Antwort auf das Thema.';
        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testReplyToThreadRedirectsWithoutLogin(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        // Create a thread first
        $crawler = $client->request('GET', '/boards/general/addthread');
        $form = $crawler->filter('button[type="submit"]')->form();
        $form['form[title]'] = 'Thread fuer Logintest';
        $form['form[message]'] = 'Erste Nachricht.';
        $client->submit($form);

        $em = static::getContainer()->get('doctrine')->getManager();
        $thread = $em->getRepository(Thread::class)->findOneBy(['title' => 'Thread fuer Logintest']);
        $this->assertNotNull($thread, 'Thread should exist after creation');
        $threadSlug = $thread->getSlug();

        // Shutdown kernel and create a new unauthenticated client
        self::ensureKernelShutdown();
        $client = static::createClient();

        $client->request('GET', sprintf('/post/write/thread/%s', $threadSlug));

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
