<?php declare(strict_types=1);

namespace Tests\Controller\V2;

use App\Design\DesignChoice;
use App\Entity\Ride;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Tests\Controller\AbstractControllerTestCase;

class MiscPagesTest extends AbstractControllerTestCase
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

    private function createV2Client(): KernelBrowser
    {
        $client = static::createClient();
        $client->getCookieJar()->set(new Cookie(DesignChoice::COOKIE, DesignChoice::V2));

        return $client;
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function publicPages(): iterable
    {
        yield 'top ten' => ['/statistic/top', '.cm-rank'];
        yield 'month' => ['/statistic/2026/9', '.cm-month-nav'];
        yield 'search' => ['/search/query?query=hamburg', 'form.cm-q input[name="query"][value="hamburg"]'];
        yield 'empty search' => ['/search/query', 'form.cm-q'];
        yield 'impress' => ['/content/impress', '.cm-prose'];
        yield 'privacy' => ['/content/privacy', '#matomo-opt-out'];
        yield 'gdpr' => ['/content/gdpr', '.cm-toc a[href="#tracking"]'];
        yield 'faq' => ['/content/faq', '.cm-faq'];
        yield 'about' => ['/content/about', '.cm-prose'];
        yield 'glympse' => ['/content/glympse', '.cm-card'];
        yield 'critical maps' => ['/content/critical-maps', '.cm-card'];
    }

    #[DataProvider('publicPages')]
    public function testPublicPageRendersInNewDesign(string $url, string $selector): void
    {
        $client = $this->createV2Client();

        $client->request('GET', $url);

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorExists($selector);
    }

    public function testSearchGroupsCitiesAndRides(): void
    {
        $client = $this->createV2Client();

        $client->request('GET', '/search/query?query=hamburg');

        self::assertSelectorTextContains('.cm-hits', 'Hamburg');
        self::assertSelectorExists('.cm-ride-hits table');
    }

    public function testLoginKeepsPasskeyAndCaptchaWithoutNavigation(): void
    {
        $client = $this->createV2Client();

        $client->request('GET', '/login');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2 .cm-solo');
        self::assertSelectorNotExists('.cm-sidenav');
        self::assertSelectorExists('[data-controller="passkey"][data-passkey-conditional-value="true"] [data-action="passkey#login"]');
        self::assertSelectorExists('input[autocomplete="username webauthn"]');
        self::assertSelectorExists('.frc-captcha');
        self::assertSelectorExists('a[href*="facebook"]');
        self::assertSelectorExists('a[href*="strava"]');
    }

    public function testRideEditFormSubmitsInNewDesign(): void
    {
        $client = $this->createV2Client();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getLatestHamburgRide();
        $url = sprintf('/%s/%s/edit', $ride->getCity()->getMainSlugString(), $ride->getDateTime()->format('Y-m-d'));

        $crawler = $client->request('GET', $url);

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2 form.cm-form[data-controller="ride-date-checker"]');
        self::assertSelectorExists('[data-controller="map--form-map"]');

        $form = $crawler->selectButton('Speichern')->form();
        $client->submit($form);

        self::assertResponseRedirects();
    }

    public function testRideAddFormRendersInNewDesign(): void
    {
        $client = $this->createV2Client();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $client->request('GET', '/hamburg/add-ride');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Tour in Hamburg eintragen');
        self::assertSelectorExists('select[name="ride[rideType]"] option[value="CRITICAL_MASS"]');
    }

    public function testCityEditFormRendersInNewDesign(): void
    {
        $client = $this->createV2Client();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $client->request('GET', '/hamburg/edit');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2 form.cm-form');
        self::assertSelectorTextContains('h1', 'Stadt bearbeiten');
        self::assertSelectorExists('[data-controller="map--form-map"]');
    }

    private function getLatestHamburgRide(): Ride
    {
        $ride = static::getContainer()->get('doctrine')->getManager()->getRepository(Ride::class)
            ->createQueryBuilder('r')
            ->join('r.city', 'c')
            ->join('c.mainSlug', 'cs')
            ->where('cs.slug = :slug')
            ->setParameter('slug', 'hamburg')
            ->orderBy('r.dateTime', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        self::assertInstanceOf(Ride::class, $ride);

        return $ride;
    }
}
