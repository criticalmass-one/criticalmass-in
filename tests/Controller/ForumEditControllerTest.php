<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\Post;
use App\Entity\Thread;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class ForumEditControllerTest extends AbstractControllerTestCase
{
    private const AUTHOR = 'testuser@criticalmass.in';

    /**
     * Legt über das Formular ein frisches Thema an und liefert dessen Slug zurück.
     */
    private function openThread(KernelBrowser $client, string $title): string
    {
        $crawler = $client->request('GET', '/boards/general/addthread');

        $form = $crawler->selectButton('Speichern')->form();
        $form['form[title]'] = $title;
        $form['form[message]'] = 'Der erste Beitrag zu ' . $title . '.';

        $client->submit($form);

        $thread = static::getContainer()->get('doctrine')->getRepository(Thread::class)
            ->findOneBy(['title' => $title]);

        self::assertNotNull($thread, 'Das Thema sollte angelegt worden sein.');

        return (string) $thread->getSlug();
    }

    private function firstPostOf(string $threadSlug): Post
    {
        $doctrine = static::getContainer()->get('doctrine');
        $thread = $doctrine->getRepository(Thread::class)->findOneBy(['slug' => $threadSlug]);

        $post = $thread?->getFirstPost();
        self::assertInstanceOf(Post::class, $post);

        return $post;
    }

    public function testAuthorCanEditTheirPost(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $slug = $this->openThread($client, 'Beitrag bearbeiten Test');
        $post = $this->firstPostOf($slug);

        $crawler = $client->request('GET', '/post/edit/' . $post->getId());
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Speichern')->form();
        $form['post[message]'] = 'Dieser Text wurde nachträglich geändert.';
        $client->submit($form);

        self::assertEquals(302, $client->getResponse()->getStatusCode());

        static::getContainer()->get('doctrine')->getManager()->clear();
        $updated = static::getContainer()->get('doctrine')->getRepository(Post::class)->find($post->getId());

        self::assertSame('Dieser Text wurde nachträglich geändert.', $updated->getMessage());
        self::assertNotNull($updated->getUpdatedAt(), 'Eine Bearbeitung muss einen Zeitstempel hinterlassen.');
    }

    public function testStrangerCannotEditSomeoneElsesPost(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);
        $slug = $this->openThread($client, 'Fremder Beitrag Test');
        $post = $this->firstPostOf($slug);

        $other = static::createClient();
        $this->loginAs($other, 'cyclist@criticalmass.in');
        $other->request('GET', '/post/edit/' . $post->getId());

        self::assertEquals(403, $other->getResponse()->getStatusCode());
    }

    public function testEditPostRequiresLogin(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);
        $slug = $this->openThread($client, 'Anonymer Zugriff Test');
        $post = $this->firstPostOf($slug);

        $anonymous = static::createClient();
        $anonymous->request('GET', '/post/edit/' . $post->getId());

        self::assertEquals(302, $anonymous->getResponse()->getStatusCode());
    }

    public function testThreadOpenerCanRenameTheThread(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);
        $slug = $this->openThread($client, 'Titel vorher');

        $crawler = $client->request('GET', '/thread/edit/' . $slug);
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Speichern')->form();
        $form['thread[title]'] = 'Titel nachher';
        $client->submit($form);

        self::assertEquals(302, $client->getResponse()->getStatusCode());

        $doctrine = static::getContainer()->get('doctrine');
        $doctrine->getManager()->clear();
        $thread = $doctrine->getRepository(Thread::class)->findOneBy(['title' => 'Titel nachher']);

        self::assertNotNull($thread);
        self::assertSame('titel-nachher', $thread->getSlug(), 'Der Slug wandert mit dem Titel mit.');
    }

    public function testRenamingKeepsSlugsUnique(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $this->openThread($client, 'Doppelter Titel');
        $secondSlug = $this->openThread($client, 'Noch ein Thema');

        $crawler = $client->request('GET', '/thread/edit/' . $secondSlug);
        $form = $crawler->selectButton('Speichern')->form();
        $form['thread[title]'] = 'Doppelter Titel';
        $client->submit($form);

        $doctrine = static::getContainer()->get('doctrine');
        $doctrine->getManager()->clear();
        $threads = $doctrine->getRepository(Thread::class)->findBy(['title' => 'Doppelter Titel']);

        self::assertCount(2, $threads);
        self::assertNotSame(
            $threads[0]->getSlug(),
            $threads[1]->getSlug(),
            'Zwei Themen dürfen nie unter derselben Adresse liegen.'
        );
    }

    public function testStrangerCannotRenameThread(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);
        $slug = $this->openThread($client, 'Fremdes Thema Test');

        $other = static::createClient();
        $this->loginAs($other, 'cyclist@criticalmass.in');
        $other->request('GET', '/thread/edit/' . $slug);

        self::assertEquals(403, $other->getResponse()->getStatusCode());
    }
}
