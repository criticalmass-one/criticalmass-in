<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\Post;
use App\Entity\Thread;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class ForumWithdrawControllerTest extends AbstractControllerTestCase
{
    private const AUTHOR = 'testuser@criticalmass.in';
    private const ADMIN = 'admin@criticalmass.in';

    private function openThread(KernelBrowser $client, string $title): string
    {
        $crawler = $client->request('GET', '/boards/general/addthread');

        $form = $crawler->selectButton('Speichern')->form();
        $form['form[title]'] = $title;
        $form['form[message]'] = 'Der erste Beitrag zu ' . $title . '.';

        $client->submit($form);

        $thread = static::getContainer()->get('doctrine')->getRepository(Thread::class)
            ->findOneBy(['title' => $title]);

        self::assertNotNull($thread);

        return (string) $thread->getSlug();
    }

    /**
     * Antwortet ueber das echte Formular. Ein roher POST scheitert an der
     * CSRF-Pruefung, und das Formular meldet den Fehler still zurueck.
     */
    private function reply(KernelBrowser $client, string $threadSlug, string $message): void
    {
        $crawler = $client->request('GET', '/post/write/thread/' . $threadSlug);

        $form = $crawler->selectButton('Speichern')->form();
        $form['post[message]'] = $message;

        $client->submit($form);

        self::assertEquals(302, $client->getResponse()->getStatusCode(), 'Die Antwort sollte gespeichert werden.');
    }

    private function thread(string $slug): Thread
    {
        $doctrine = static::getContainer()->get('doctrine');
        $doctrine->getManager()->clear();

        $thread = $doctrine->getRepository(Thread::class)->findOneBy(['slug' => $slug]);
        self::assertInstanceOf(Thread::class, $thread);

        return $thread;
    }

    public function testAuthorCanWithdrawTheirReply(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $slug = $this->openThread($client, 'Antwort zuruecknehmen');
        $this->reply($client, $slug, 'Diese Antwort verschwindet gleich wieder.');

        $reply = $this->thread($slug)->getLastPost();
        self::assertInstanceOf(Post::class, $reply);
        $replyId = $reply->getId();

        $client->request('POST', '/post/disable/' . $replyId);
        self::assertEquals(302, $client->getResponse()->getStatusCode());

        $doctrine = static::getContainer()->get('doctrine');
        $doctrine->getManager()->clear();

        self::assertFalse($doctrine->getRepository(Post::class)->find($replyId)->getEnabled());
    }

    public function testFirstPostCannotBeWithdrawnOnItsOwn(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $slug = $this->openThread($client, 'Erster Beitrag bleibt');
        $firstPost = $this->thread($slug)->getFirstPost();
        self::assertInstanceOf(Post::class, $firstPost);

        $client->request('POST', '/post/disable/' . $firstPost->getId());

        self::assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testThreadOpenerCanWithdrawTheWholeThread(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $slug = $this->openThread($client, 'Ganzes Thema zurueckziehen');

        $client->request('POST', '/thread/disable/' . $slug);
        self::assertEquals(302, $client->getResponse()->getStatusCode());

        $doctrine = static::getContainer()->get('doctrine');
        $doctrine->getManager()->clear();

        $thread = $doctrine->getRepository(Thread::class)->findOneBy(['slug' => $slug]);
        self::assertFalse($thread->getEnabled() ?? true);
    }

    public function testStrangerCannotWithdrawSomeoneElsesThread(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);
        $slug = $this->openThread($client, 'Fremdes Thema bleibt');

        $this->loginAs($client, 'cyclist@criticalmass.in');
        $client->request('POST', '/thread/disable/' . $slug);

        self::assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testAdminCanMoveAThreadToAnotherBoard(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);
        $slug = $this->openThread($client, 'Umzugswilliges Thema');

        $this->loginAs($client, self::ADMIN);
        $crawler = $client->request('GET', '/thread/move/' . $slug);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertGreaterThan(0, $crawler->filter('select')->count(), 'Die Zielauswahl muss angeboten werden.');
    }

    public function testAuthorCannotMoveAThread(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);
        $slug = $this->openThread($client, 'Nicht verschiebbares Thema');

        $client->request('GET', '/thread/move/' . $slug);

        self::assertEquals(403, $client->getResponse()->getStatusCode());
    }
}
