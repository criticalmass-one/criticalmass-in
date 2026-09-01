<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Controller\BoardController;
use App\Entity\Thread;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class ForumPaginationControllerTest extends AbstractControllerTestCase
{
    private const AUTHOR = 'testuser@criticalmass.in';

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

    public function testThreadListStaysReachableWithAPageParameter(): void
    {
        $client = static::createClient();

        $client->request('GET', '/boards/general', ['page' => 1]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testThreadViewStaysReachableWithAPageParameter(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $slug = $this->openThread($client, 'Seitenzahl im Thema');

        $client->request('GET', '/boards/general/thread/' . $slug, ['page' => 1]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShortThreadShowsNoPagination(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $slug = $this->openThread($client, 'Kurzes Thema ohne Seiten');
        $crawler = $client->request('GET', '/boards/general/thread/' . $slug);

        self::assertSame(
            0,
            $crawler->filter('nav[aria-label="Seiten"]')->count(),
            'Ein Thema mit einem Beitrag braucht keine Seitennavigation.'
        );
    }

    public function testLongThreadIsSplitIntoPages(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $slug = $this->openThread($client, 'Langes Thema mit Seiten');

        // Ein Beitrag mehr als auf eine Seite passt.
        for ($i = 0; $i < BoardController::POSTS_PER_PAGE; ++$i) {
            $client->request('POST', '/post/write/thread/' . $slug, ['post' => ['message' => 'Antwort Nummer ' . $i]]);
        }

        $crawler = $client->request('GET', '/boards/general/thread/' . $slug);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertGreaterThan(0, $crawler->filter('nav[aria-label="Seiten"]')->count());
        self::assertGreaterThan(0, $crawler->filter('link[rel="next"]')->count(), 'Suchmaschinen brauchen den Vorwärtsverweis.');

        $secondPage = $client->request('GET', '/boards/general/thread/' . $slug, ['page' => 2]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertGreaterThan(0, $secondPage->filter('link[rel="prev"]')->count());
    }

    public function testEditRedirectLandsOnThePageHoldingThePost(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $slug = $this->openThread($client, 'Bearbeiten auf Seite zwei');

        for ($i = 0; $i < BoardController::POSTS_PER_PAGE; ++$i) {
            $client->request('POST', '/post/write/thread/' . $slug, ['post' => ['message' => 'Antwort Nummer ' . $i]]);
        }

        $doctrine = static::getContainer()->get('doctrine');
        $doctrine->getManager()->clear();
        $lastPost = $doctrine->getRepository(Thread::class)->findOneBy(['slug' => $slug])->getLastPost();

        $crawler = $client->request('GET', '/post/edit/' . $lastPost->getId());
        $form = $crawler->selectButton('Speichern')->form();
        $form['post[message]'] = 'Nachträglich geändert.';
        $client->submit($form);

        self::assertStringContainsString(
            'page=2',
            (string) $client->getResponse()->headers->get('Location'),
            'Der Dauerlink muss auf die Seite zeigen, auf der der Beitrag wirklich steht.'
        );
    }
}
