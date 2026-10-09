<?php declare(strict_types=1);

namespace Tests\Controller\V2;

use App\Design\DesignChoice;
use App\Entity\Thread;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Tests\Controller\AbstractControllerTestCase;

class ForumTest extends AbstractControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $_ENV['FEATURE_DESIGN_V2'] = 'true';
    }

    protected function tearDown(): void
    {
        unset($_ENV['FEATURE_DESIGN_V2']);

        parent::tearDown();
    }

    private function createV2Client(?string $email = null): KernelBrowser
    {
        $client = static::createClient();
        $client->getCookieJar()->set(new Cookie(DesignChoice::COOKIE, DesignChoice::V2));

        if (null !== $email) {
            $this->loginAs($client, $email);
        }

        return $client;
    }

    private function createThread(KernelBrowser $client, string $title): Thread
    {
        $crawler = $client->request('GET', '/boards/general/addthread');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorExists('.cm-editor[data-controller="markdown-editor"]');

        $form = $crawler->selectButton('Thema anlegen')->form();
        $form['form[title]'] = $title;
        $form['form[message]'] = 'Erster Beitrag zu ' . $title;
        $client->submit($form);

        self::assertResponseRedirects();

        $thread = static::getContainer()->get('doctrine')->getRepository(Thread::class)->findOneBy(['title' => $title]);
        self::assertInstanceOf(Thread::class, $thread);

        return $thread;
    }

    public function testOverviewAndBoardList(): void
    {
        $client = $this->createV2Client();

        $client->request('GET', '/boards/overview');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorTextContains('.cm-forum-head h1', 'Forum');
        self::assertSelectorExists('form.cm-find[action="/boards/search"]');
        self::assertSelectorTextContains('.cm-sidenav a.is-active', 'Forum');

        $client->request('GET', '/boards/general');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorExists('.cm-boards');
    }

    public function testCityForumList(): void
    {
        $client = $this->createV2Client('testuser@criticalmass.in');

        $client->request('GET', '/hamburg/listthreads');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorExists('a.cm-btn-go[href="/hamburg/addthread"]');
    }

    public function testThreadWithReplyForm(): void
    {
        $client = $this->createV2Client('testuser@criticalmass.in');
        $thread = $this->createThread($client, 'Thema in der neuen Ansicht ' . uniqid());

        $crawler = $client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorCount(1, '.cm-entry');
        self::assertSelectorExists('#antworten #markdown-editor-reply');

        $form = $crawler->selectButton('Antwort senden')->form();
        $form['post[text]'] = 'Antwort aus der neuen Ansicht';
        $client->submit($form);

        self::assertResponseRedirects();
        $client->followRedirect();
        self::assertSelectorCount(2, '.cm-entry');
        self::assertSelectorTextContains('.cm-entries', 'Antwort aus der neuen Ansicht');
        self::assertSelectorExists('button[data-controller="quote-post"]');

        $client->request('GET', '/thread/edit/' . $thread->getSlug());
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');

        $postId = $thread->getFirstPost()?->getId();
        $client->request('GET', '/post/edit/' . $postId);
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorExists('.cm-editor textarea');
    }

    public function testModerationPages(): void
    {
        $client = $this->createV2Client('admin@criticalmass.in');
        $thread = $this->createThread($client, 'Thema zum Verschieben ' . uniqid());

        $client->followRedirect();
        self::assertSelectorExists('.cm-forum-mod form[action$="/thread/pin/' . $thread->getSlug() . '"]');
        self::assertSelectorExists('.cm-forum-mod form[action$="/thread/lock/' . $thread->getSlug() . '"]');

        $client->request('GET', '/thread/move/' . $thread->getSlug());
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorExists('select.form-select');
    }

    public function testSearchProfileAndSubscriptions(): void
    {
        $client = $this->createV2Client('testuser@criticalmass.in');

        $client->request('GET', '/boards/search?q=Ansicht');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');

        $client->request('GET', '/forum/user/testuser');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorTextContains('.cm-forum-profile h1', 'testuser');

        $client->request('GET', '/forum/subscriptions');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorExists('#forum-notifications[data-action="change->auto-submit#submit"]');
    }
}
