<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\Thread;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class ForumModerationControllerTest extends AbstractControllerTestCase
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

    private function reloadThread(string $slug): Thread
    {
        $doctrine = static::getContainer()->get('doctrine');
        $doctrine->getManager()->clear();

        $thread = $doctrine->getRepository(Thread::class)->findOneBy(['slug' => $slug]);
        self::assertInstanceOf(Thread::class, $thread);

        return $thread;
    }

    public function testNewThreadsAreNeitherLockedNorSticky(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $thread = $this->reloadThread($this->openThread($client, 'Frisches Thema'));

        self::assertFalse($thread->isLocked());
        self::assertFalse($thread->isSticky());
    }

    public function testAdminCanLockAndReopenAThread(): void
    {
        $author = static::createClient();
        $this->loginAs($author, self::AUTHOR);
        $slug = $this->openThread($author, 'Zu schliessendes Thema');

        $admin = static::createClient();
        $this->loginAs($admin, self::ADMIN);

        $admin->request('POST', '/thread/lock/' . $slug);
        self::assertEquals(302, $admin->getResponse()->getStatusCode());
        self::assertTrue($this->reloadThread($slug)->isLocked());

        $admin->request('POST', '/thread/lock/' . $slug);
        self::assertFalse($this->reloadThread($slug)->isLocked(), 'Derselbe Endpunkt öffnet wieder.');
    }

    public function testAuthorCannotLockTheirOwnThread(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);
        $slug = $this->openThread($client, 'Selbst schliessen verboten');

        $client->request('POST', '/thread/lock/' . $slug);

        self::assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testAdminCanPinAndUnpinAThread(): void
    {
        $author = static::createClient();
        $this->loginAs($author, self::AUTHOR);
        $slug = $this->openThread($author, 'Anzuheftendes Thema');

        $admin = static::createClient();
        $this->loginAs($admin, self::ADMIN);

        $admin->request('POST', '/thread/pin/' . $slug);
        self::assertTrue($this->reloadThread($slug)->isSticky());

        $admin->request('POST', '/thread/pin/' . $slug);
        self::assertFalse($this->reloadThread($slug)->isSticky());
    }

    public function testLockedThreadRejectsReplies(): void
    {
        $author = static::createClient();
        $this->loginAs($author, self::AUTHOR);
        $slug = $this->openThread($author, 'Geschlossen fuer Antworten');

        $admin = static::createClient();
        $this->loginAs($admin, self::ADMIN);
        $admin->request('POST', '/thread/lock/' . $slug);

        $author->request('POST', '/post/write/thread/' . $slug, ['post' => ['message' => 'Trotzdem!']]);

        self::assertEquals(403, $author->getResponse()->getStatusCode());
    }

    public function testLockedThreadStaysReadable(): void
    {
        $author = static::createClient();
        $this->loginAs($author, self::AUTHOR);
        $slug = $this->openThread($author, 'Lesbar trotz Schloss');

        $admin = static::createClient();
        $this->loginAs($admin, self::ADMIN);
        $admin->request('POST', '/thread/lock/' . $slug);

        $crawler = $author->request('GET', '/boards/general/thread/' . $slug);

        self::assertEquals(200, $author->getResponse()->getStatusCode());
        self::assertStringContainsString('geschlossen', $crawler->filter('body')->text());
    }

    public function testStickyThreadsAreListedFirst(): void
    {
        $author = static::createClient();
        $this->loginAs($author, self::AUTHOR);

        $olderSlug = $this->openThread($author, 'Aelteres Thema oben');
        $this->openThread($author, 'Neueres Thema unten');

        $admin = static::createClient();
        $this->loginAs($admin, self::ADMIN);
        $admin->request('POST', '/thread/pin/' . $olderSlug);

        $crawler = $author->request('GET', '/boards/general');
        $titles = $crawler->filter('.card-body strong')->each(fn ($node) => trim($node->text()));

        $pinnedPosition = array_search('Aelteres Thema oben', $titles, true);
        $normalPosition = array_search('Neueres Thema unten', $titles, true);

        self::assertNotFalse($pinnedPosition);
        self::assertNotFalse($normalPosition);
        self::assertLessThan($normalPosition, $pinnedPosition, 'Angeheftete Themen stehen oben.');
    }
}
