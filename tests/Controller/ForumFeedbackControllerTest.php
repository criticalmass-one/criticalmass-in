<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\Thread;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

/**
 * Die Rückmeldung nach einer Aktion.
 *
 * Beides hier ist im Browser aufgefallen, nicht in einem Test: Das Layout gab
 * Flash-Meldungen überhaupt nicht aus, und der Abo-Knopf hiess auch dann
 * „Abonnieren“, wenn man bereits abonniert hatte — ein Klick bestellte dann
 * stillschweigend ab.
 */
class ForumFeedbackControllerTest extends AbstractControllerTestCase
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

    private function tokenFrom(KernelBrowser $client, string $pageUrl, string $actionFragment): string
    {
        $crawler = $client->request('GET', $pageUrl);
        $field = $crawler->filter(sprintf('form[action*="%s"] input[name="_token"]', $actionFragment));

        self::assertGreaterThan(0, $field->count());

        return (string) $field->first()->attr('value');
    }

    public function testAnActionLeavesAVisibleMessage(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $slug = $this->openThread($client, 'Rueckmeldung sichtbar');
        $threadUrl = '/boards/general/thread/' . $slug;

        $client->request('POST', '/forum/subscribe/thread/' . $slug, [
            '_token' => $this->tokenFrom($client, $threadUrl, '/forum/subscribe/thread/'),
        ]);

        $crawler = $client->followRedirect();

        self::assertGreaterThan(
            0,
            $crawler->filter('.alert')->count(),
            'Nach einer Aktion muss eine Rückmeldung im Seitenkopf stehen.'
        );
    }

    public function testTheSubscribeButtonShowsTheCurrentState(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::AUTHOR);

        $slug = $this->openThread($client, 'Abo-Knopf zeigt Zustand');
        $threadUrl = '/boards/general/thread/' . $slug;

        // Wer ein Thema eröffnet, abonniert es automatisch mit.
        $crawler = $client->request('GET', $threadUrl);
        self::assertStringContainsString(
            'Abbestellen',
            $crawler->filter('form[action*="/forum/subscribe/thread/"]')->text(),
            'Wer schon abonniert hat, bekommt „Abbestellen“ angeboten.'
        );

        $client->request('POST', '/forum/subscribe/thread/' . $slug, [
            '_token' => $this->tokenFrom($client, $threadUrl, '/forum/subscribe/thread/'),
        ]);

        $crawler = $client->request('GET', $threadUrl);
        self::assertStringContainsString(
            'Abonnieren',
            $crawler->filter('form[action*="/forum/subscribe/thread/"]')->text()
        );
    }
}
