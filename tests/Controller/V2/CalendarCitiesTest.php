<?php declare(strict_types=1);

namespace Tests\Controller\V2;

use App\Design\DesignChoice;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Tests\Controller\AbstractControllerTestCase;

class CalendarCitiesTest extends AbstractControllerTestCase
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

    private function v2Client(): KernelBrowser
    {
        $client = static::createClient();
        $client->getCookieJar()->set(new Cookie(DesignChoice::COOKIE, DesignChoice::V2));

        return $client;
    }

    public function testCalendar(): void
    {
        $client = $this->v2Client();
        $client->request('GET', '/calendar');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorExists('.cm-cal-grid a[aria-current="date"]');
        self::assertSelectorExists('.cm-cal-step a[rel="next"]');
    }

    public function testCalendarDay(): void
    {
        $client = $this->v2Client();
        $client->request('GET', '/calendar?year=2026&month=2&day=13');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorTextContains('.cm-cal-day h2', '13. Februar');
    }

    public function testCityList(): void
    {
        $client = $this->v2Client();
        $client->request('GET', '/citylist');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorExists('#city-list-table .cm-cl-tbl');
        self::assertSelectorTextContains('#city-list-table', 'Hamburg');
    }

    public function testRegionWorld(): void
    {
        $client = $this->v2Client();
        $client->request('GET', '/world');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorTextContains('h1', 'Critical Mass weltweit');
        self::assertSelectorExists('.cm-rg-list a');
    }

    public function testRegionStateOffersCityAddWhenLoggedIn(): void
    {
        $client = $this->v2Client();
        $this->loginAs($client, 'cyclist@criticalmass.in');
        $client->request('GET', '/world/europe/germany/hamburg');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorExists('#region-map[data-controller="map--query-map"]');
        self::assertSelectorTextContains('.cm-cl-links', 'Stadt hinzufügen');
    }

    public function testExplore(): void
    {
        $client = $this->v2Client();
        $client->request('GET', '/explore');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorExists('.cm-shell-map [data-controller="map--explore-map"] .explore-map');
        self::assertSelectorExists('[data-map--explore-map-target="sidebarList"]');
        self::assertSelectorTextContains('.cm-sidenav a.is-active', 'Karte');
    }
}
