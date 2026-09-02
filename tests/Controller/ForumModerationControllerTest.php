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

    /**
     * Holt das Formular-Token aus der Seite. Die Moderationsformulare sind von Hand
     * geschrieben, ihre Token stehen als verstecktes Feld im Markup.
     */
    private function tokenFrom(KernelBrowser $client, string $pageUrl, string $actionFragment): string
    {
        $crawler = $client->request('GET', $pageUrl);
        $field = $crawler->filter(sprintf('form[action*="%s"] input[name="_token"]', $actionFragment));

        self::assertGreaterThan(0, $field->count(), 'Das Formular sollte ein Token mitliefern.');

        return (string) $field->first()->attr('value');
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
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);
        $slug = $this->openThread($client, 'Zu schliessendes Thema');

        $this->loginAs($client, self::ADMIN);

        $client->request('POST', '/thread/lock/' . $slug, ['_token' => $this->tokenFrom($client, '/boards/general/thread/' . $slug, '/thread/lock/')]);
        self::assertEquals(302, $client->getResponse()->getStatusCode());
        self::assertTrue($this->reloadThread($slug)->isLocked());

        $client->request('POST', '/thread/lock/' . $slug, ['_token' => $this->tokenFrom($client, '/boards/general/thread/' . $slug, '/thread/lock/')]);
        self::assertFalse($this->reloadThread($slug)->isLocked(), 'Derselbe Endpunkt öffnet wieder.');
    }

    public function testAuthorCannotLockTheirOwnThread(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);
        $slug = $this->openThread($client, 'Selbst schliessen verboten');

        // Ohne Berechtigung wird das Formular gar nicht erst angezeigt; die Rechtepruefung
        // steht vor der Token-Pruefung, ein beliebiges Token genuegt also fuer den Test.
        $client->request('POST', '/thread/lock/' . $slug, ['_token' => 'egal']);

        self::assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testAdminCanPinAndUnpinAThread(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);
        $slug = $this->openThread($client, 'Anzuheftendes Thema');

        $this->loginAs($client, self::ADMIN);

        $client->request('POST', '/thread/pin/' . $slug, ['_token' => $this->tokenFrom($client, '/boards/general/thread/' . $slug, '/thread/pin/')]);
        self::assertTrue($this->reloadThread($slug)->isSticky());

        $client->request('POST', '/thread/pin/' . $slug, ['_token' => $this->tokenFrom($client, '/boards/general/thread/' . $slug, '/thread/pin/')]);
        self::assertFalse($this->reloadThread($slug)->isSticky());
    }

    public function testLockedThreadRejectsReplies(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);
        $slug = $this->openThread($client, 'Geschlossen fuer Antworten');

        $this->loginAs($client, self::ADMIN);
        $client->request('POST', '/thread/lock/' . $slug, ['_token' => $this->tokenFrom($client, '/boards/general/thread/' . $slug, '/thread/lock/')]);

        $this->loginAs($client, self::AUTHOR);
        $client->request('POST', '/post/write/thread/' . $slug, ['post' => ['message' => 'Trotzdem!']]);

        self::assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testLockedThreadStaysReadable(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);
        $slug = $this->openThread($client, 'Lesbar trotz Schloss');

        $this->loginAs($client, self::ADMIN);
        $client->request('POST', '/thread/lock/' . $slug, ['_token' => $this->tokenFrom($client, '/boards/general/thread/' . $slug, '/thread/lock/')]);

        $crawler = $client->request('GET', '/boards/general/thread/' . $slug);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('geschlossen', $crawler->filter('body')->text());
    }

    public function testStickyThreadsAreListedFirst(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $olderSlug = $this->openThread($client, 'Aelteres Thema oben');
        $this->openThread($client, 'Neueres Thema unten');

        $this->loginAs($client, self::ADMIN);
        $client->request('POST', '/thread/pin/' . $olderSlug, ['_token' => $this->tokenFrom($client, '/boards/general/thread/' . $olderSlug, '/thread/pin/')]);

        $crawler = $client->request('GET', '/boards/general');
        $titles = $crawler->filter('.card-body strong')->each(fn ($node) => trim($node->text()));

        $pinnedPosition = array_search('Aelteres Thema oben', $titles, true);
        $normalPosition = array_search('Neueres Thema unten', $titles, true);

        self::assertNotFalse($pinnedPosition);
        self::assertNotFalse($normalPosition);
        self::assertLessThan($normalPosition, $pinnedPosition, 'Angeheftete Themen stehen oben.');
    }
}
