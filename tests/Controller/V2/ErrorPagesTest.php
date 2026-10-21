<?php declare(strict_types=1);

namespace Tests\Controller\V2;

use App\Design\DesignChoice;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Tests\Controller\AbstractControllerTestCase;

/**
 * Die Fehlerseiten laufen über /_error/{code}, weil der Kernel im Debug-Modus sonst die
 * Ausnahme zeigt statt der Vorlage. Unter PHPUnit hält sich der Kernel für eine
 * Kommandozeile und würde den CLI-Renderer wählen, deshalb wird der Web-Modus gesetzt.
 */
class ErrorPagesTest extends AbstractControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $_ENV['FEATURE_DESIGN_V2'] = 'true';
        $_ENV['APP_RUNTIME_MODE'] = 'web=1';
    }

    protected function tearDown(): void
    {
        unset($_ENV['FEATURE_DESIGN_V2'], $_ENV['APP_RUNTIME_MODE']);

        parent::tearDown();
    }

    private function createV2Client(): KernelBrowser
    {
        $client = static::createClient();
        $client->getCookieJar()->set(new Cookie(DesignChoice::COOKIE, DesignChoice::V2));

        return $client;
    }

    public function testNotFoundInNewDesign(): void
    {
        $client = $this->createV2Client();

        $client->request('GET', '/_error/404');

        self::assertResponseStatusCodeSame(404);
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorTextContains('.cm-page-head h1', 'Seite nicht gefunden');
        self::assertSelectorExists('.cm-btn-row a[href="/"]');
        self::assertSelectorExists('meta[name="robots"][content*="noindex"]');
    }

    public function testForbiddenInNewDesignOffersLogin(): void
    {
        $client = $this->createV2Client();

        $client->request('GET', '/_error/403');

        self::assertResponseStatusCodeSame(403);
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorTextContains('.cm-page-head h1', 'Kein Zugriff');
        self::assertSelectorExists('.cm-btn-row a[href="/login"]');
        self::assertSelectorExists('meta[name="robots"][content*="noindex"]');
    }

    public function testForbiddenInNewDesignHidesLoginWhenSignedIn(): void
    {
        $client = $this->createV2Client();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $client->request('GET', '/_error/403');

        self::assertResponseStatusCodeSame(403);
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorNotExists('.cm-btn-row a[href="/login"]');
    }

    public function testNotFoundInOldDesign(): void
    {
        $client = static::createClient();

        $client->request('GET', '/_error/404');

        self::assertResponseStatusCodeSame(404);
        self::assertSelectorNotExists('body.cm-v2');
        self::assertSelectorExists('nav.navbar');
        self::assertSelectorTextContains('h1', 'Seite nicht gefunden');
        self::assertSelectorExists('meta[name="robots"][content*="noindex"]');
    }

    public function testForbiddenInOldDesignOffersLogin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/_error/403');

        self::assertResponseStatusCodeSame(403);
        self::assertSelectorNotExists('body.cm-v2');
        self::assertSelectorExists('nav.navbar');
        self::assertSelectorTextContains('h1', 'Kein Zugriff');
        self::assertSelectorExists('.card-body a[href="/login"]');
        self::assertSelectorExists('meta[name="robots"][content*="noindex"]');
    }

    public function testForbiddenInOldDesignHidesLoginWhenSignedIn(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $client->request('GET', '/_error/403');

        self::assertResponseStatusCodeSame(403);
        self::assertSelectorNotExists('body.cm-v2');
        self::assertSelectorNotExists('.card-body a[href="/login"]');
    }

    public function testMaintenanceErrorHasGermanText(): void
    {
        $client = static::createClient();

        $client->request('GET', '/_error/503');

        self::assertResponseStatusCodeSame(503);
        self::assertSelectorTextContains('h1', 'Fehler 503');
        self::assertSelectorTextContains('main p', 'Die Seite wird gerade gewartet.');
    }

    public function testServerErrorStaysStandaloneInNewDesign(): void
    {
        $this->assertStandaloneServerError($this->createV2Client());
    }

    public function testServerErrorStaysStandaloneInOldDesign(): void
    {
        $this->assertStandaloneServerError(static::createClient());
    }

    private function assertStandaloneServerError(KernelBrowser $client): void
    {
        $client->request('GET', '/_error/500');

        self::assertResponseStatusCodeSame(500);
        self::assertSelectorNotExists('body.cm-v2');
        self::assertSelectorNotExists('nav');
        self::assertSelectorNotExists('link[rel="stylesheet"]');
        self::assertSelectorTextContains('h1', 'Fehler 500');
        self::assertSelectorExists('a[href="/"]');
    }
}
