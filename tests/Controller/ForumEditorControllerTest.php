<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\Thread;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class ForumEditorControllerTest extends AbstractControllerTestCase
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

    public function testPreviewRendersMarkdown(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $client->request('POST', '/post/preview', ['message' => 'Das ist **fett** und _kursiv_.']);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $html = (string) $client->getResponse()->getContent();

        self::assertStringContainsString('<strong>fett</strong>', $html);
        self::assertStringContainsString('<em>kursiv</em>', $html);
    }

    public function testPreviewRendersQuotesAsBlockquote(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $client->request('POST', '/post/preview', ['message' => "> zitierter Text\n\nmeine Antwort"]);

        self::assertStringContainsString('<blockquote>', (string) $client->getResponse()->getContent());
    }

    public function testPreviewStripsRawHtml(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $client->request('POST', '/post/preview', ['message' => '<script>alert(1)</script> harmlos']);

        $html = (string) $client->getResponse()->getContent();

        self::assertStringNotContainsString('<script>', $html, 'Der Parser muss rohes HTML entfernen.');
    }

    public function testEmptyPreviewSaysSo(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $client->request('POST', '/post/preview', ['message' => '   ']);

        self::assertStringContainsString('Noch nichts geschrieben', (string) $client->getResponse()->getContent());
    }

    public function testPreviewRequiresLogin(): void
    {
        $client = static::createClient();

        $client->request('POST', '/post/preview', ['message' => 'Hallo']);

        self::assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testThreadViewOffersQuoteButtons(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $slug = $this->openThread($client, 'Zitieren im Thema');
        $crawler = $client->request('GET', '/boards/general/thread/' . $slug);

        self::assertGreaterThan(
            0,
            $crawler->filter('[data-controller="quote-post"]')->count(),
            'Angemeldete Nutzer sollen jeden Beitrag zitieren können.'
        );
    }

    public function testWriteFormCarriesTheEditor(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $crawler = $client->request('GET', '/boards/general/addthread');

        self::assertGreaterThan(0, $crawler->filter('[data-controller="markdown-editor"]')->count());
        self::assertGreaterThan(0, $crawler->filter('[data-format="bold"]')->count(), 'Die Werkzeugleiste gehört dazu.');
    }
}
