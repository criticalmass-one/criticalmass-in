<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\Thread;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class ForumSearchControllerTest extends AbstractControllerTestCase
{
    private const AUTHOR = 'testuser@criticalmass.in';

    private function openThread(KernelBrowser $client, string $title, string $message): string
    {
        $crawler = $client->request('GET', '/boards/general/addthread');

        $form = $crawler->selectButton('Speichern')->form();
        $form['form[title]'] = $title;
        $form['form[message]'] = $message;

        $client->submit($form);

        $thread = static::getContainer()->get('doctrine')->getRepository(Thread::class)
            ->findOneBy(['title' => $title]);

        self::assertNotNull($thread);

        return (string) $thread->getSlug();
    }

    public function testSearchPageIsPublic(): void
    {
        $client = static::createClient();

        $client->request('GET', '/boards/search');

        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShortTermsAreRejectedWithAHint(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/boards/search', ['q' => 'ab']);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('mindestens', $crawler->filter('body')->text());
    }

    public function testFindsAPostByItsText(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $this->openThread($client, 'Suchbares Thema', 'Hier steht das Wort Klingelbeutel im Text.');

        $crawler = $client->request('GET', '/boards/search', ['q' => 'Klingelbeutel']);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('Suchbares Thema', $crawler->filter('body')->text());
        self::assertGreaterThan(0, $crawler->filter('mark')->count(), 'Der Treffer wird hervorgehoben.');
    }

    public function testFindsAThreadByItsTitle(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $this->openThread($client, 'Regenbogenforelle als Titel', 'Der Text sagt nichts dazu.');

        $crawler = $client->request('GET', '/boards/search', ['q' => 'Regenbogenforelle']);

        self::assertStringContainsString('Regenbogenforelle', $crawler->filter('body')->text());
    }

    public function testSaysSoWhenNothingMatches(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/boards/search', ['q' => 'Quastenflosserfahrrad']);

        self::assertStringContainsString('Nichts gefunden', $crawler->filter('body')->text());
    }

    public function testWithdrawnPostsDoNotShowUp(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $slug = $this->openThread($client, 'Thema mit Rueckzug', 'Der erste Beitrag.');

        // Ueber das echte Formular, sonst scheitert der Beitrag an der CSRF-Pruefung
        // und der Test bestuende, ohne je etwas zurueckgezogen zu haben.
        $crawler = $client->request('GET', '/post/write/thread/' . $slug);
        $form = $crawler->selectButton('Speichern')->form();
        $form['post[message]'] = 'Stichwort Rueckzugswort hier.';
        $client->submit($form);

        $doctrine = static::getContainer()->get('doctrine');
        $doctrine->getManager()->clear();
        $reply = $doctrine->getRepository(Thread::class)->findOneBy(['slug' => $slug])->getLastPost();

        $crawler = $client->request('GET', '/boards/general/thread/' . $slug);
        $token = (string) $crawler->filter('form[action*="/post/disable/' . $reply->getId() . '"] input[name="_token"]')->first()->attr('value');
        $client->request('POST', '/post/disable/' . $reply->getId(), ['_token' => $token]);

        $crawler = $client->request('GET', '/boards/search', ['q' => 'Rueckzugswort']);

        self::assertStringContainsString('Nichts gefunden', $crawler->filter('body')->text());
    }

    public function testOverviewLinksToTheSearch(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/boards/overview');

        self::assertGreaterThan(
            0,
            $crawler->filter('form[action="/boards/search"]')->count(),
            'Die Übersicht bietet die Suche an.'
        );
    }
}
