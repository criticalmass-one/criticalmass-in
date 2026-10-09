<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Design\DesignChoice;
use Symfony\Component\BrowserKit\Cookie;

class FrontpageDesignV2Test extends AbstractControllerTestCase
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

    public function testNewDesignRendersTheV2Frontpage(): void
    {
        $client = static::createClient();
        $client->getCookieJar()->set(new Cookie(DesignChoice::COOKIE, DesignChoice::V2));

        // Ohne gebaute Assets (CI) gibt Encore keine <link>-Tags aus.
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorExists('.cm-sidenav');
        self::assertSelectorTextContains('.cm-sidenav a.is-active', 'Timeline');
        self::assertSelectorNotExists('#navigation');
    }

    public function testOldDesignStaysUntouched(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('body.cm-v2');
        self::assertSelectorExists('#navigation');
    }

    public function testTimelineCacheKeepsDesignsApart(): void
    {
        $client = static::createClient();
        $client->getCookieJar()->set(new Cookie(DesignChoice::COOKIE, DesignChoice::V2));
        $client->request('GET', '/');
        $newHtml = (string) $client->getResponse()->getContent();

        $client->getCookieJar()->clear();
        $client->request('GET', '/');
        $oldHtml = (string) $client->getResponse()->getContent();

        self::assertStringNotContainsString('class="cm-card cm-post', $oldHtml);
        self::assertStringNotContainsString('class="card border-0 shadow-sm mb-3', $newHtml);
    }

    public function testAccountChoiceWinsOverCookie(): void
    {
        $client = static::createClient();
        $em = static::getContainer()->get('doctrine')->getManager();

        $user = $this->getUser('cyclist@criticalmass.in');
        $user->setDesignV2(true);
        $em->flush();

        $this->loginAs($client, 'cyclist@criticalmass.in');
        $client->getCookieJar()->set(new Cookie(DesignChoice::COOKIE, DesignChoice::V1));

        $client->request('GET', '/');

        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorTextContains('.cm-me', $user->getUsername());
        self::assertSelectorExists('.cm-sidenav a.cm-upload[href="/upload"]');

        $user = $this->getUser('cyclist@criticalmass.in');
        $user->setDesignV2(false);
        static::getContainer()->get('doctrine')->getManager()->flush();
        $client->getCookieJar()->set(new Cookie(DesignChoice::COOKIE, DesignChoice::V2));

        $client->request('GET', '/');

        self::assertSelectorNotExists('body.cm-v2');

        $user = $this->getUser('cyclist@criticalmass.in');
        $user->setDesignV2(null);
        static::getContainer()->get('doctrine')->getManager()->flush();
    }

    public function testUnconvertedPageStaysOldInNewDesign(): void
    {
        $client = static::createClient();
        $client->getCookieJar()->set(new Cookie(DesignChoice::COOKIE, DesignChoice::V2));

        $client->request('GET', '/login');

        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('body.cm-v2');
        self::assertSelectorExists('#navigation');
        self::assertSelectorTextContains('footer', 'Zur bisherigen Ansicht');
    }
}
