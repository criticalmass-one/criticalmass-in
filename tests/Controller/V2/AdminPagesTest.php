<?php declare(strict_types=1);

namespace Tests\Controller\V2;

use App\Design\DesignChoice;
use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Location;
use App\Entity\Promotion;
use App\Entity\Ride;
use App\Entity\SocialNetworkProfile;
use App\Model\RideGenerator\CycleExecutable;
use Doctrine\ORM\EntityManagerInterface;
use League\Bundle\OAuth2ServerBundle\Model\Client;
use League\Bundle\OAuth2ServerBundle\ValueObject\Scope;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Tests\Controller\AbstractControllerTestCase;
use Twig\Environment;

class AdminPagesTest extends AbstractControllerTestCase
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

    private function createV2Client(bool $login = true): KernelBrowser
    {
        $client = static::createClient();
        $client->getCookieJar()->set(new Cookie(DesignChoice::COOKIE, DesignChoice::V2));

        if ($login) {
            $this->loginAs($client, 'cyclist@criticalmass.in');
        }

        return $client;
    }

    private function em(): EntityManagerInterface
    {
        return static::getContainer()->get(EntityManagerInterface::class);
    }

    private function hamburg(): City
    {
        $city = $this->em()->getRepository(City::class)->findOneBy(['city' => 'Hamburg']);
        self::assertInstanceOf(City::class, $city);

        return $city;
    }

    private function cycle(): CityCycle
    {
        $cycle = $this->em()->getRepository(CityCycle::class)->findOneBy(['city' => $this->hamburg()]);
        self::assertInstanceOf(CityCycle::class, $cycle);

        return $cycle;
    }

    private function assertV2(string $selector): void
    {
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorExists($selector);
    }

    public function testCycleList(): void
    {
        $client = $this->createV2Client();
        $cycle = $this->cycle();

        $client->request('GET', '/hamburg/cycles/list');

        $this->assertV2('.cm-adm-rows');
        self::assertSelectorExists('a[href="/hamburg/cycles/add"]');
        self::assertSelectorExists(sprintf('a[href="/hamburg/cycles/%d/execute"]', $cycle->getId()));
        self::assertSelectorExists(sprintf('form[action="/hamburg/cycles/%d/disable"] input[name="_token"]', $cycle->getId()));
    }

    public function testCycleRideList(): void
    {
        $client = $this->createV2Client();
        $cycle = $this->cycle();

        $client->request('GET', sprintf('/hamburg/cycles/%d/list', $cycle->getId()));

        $this->assertV2('.cm-adm-summary .cm-dl');
    }

    public function testCycleEditShowsLocalTime(): void
    {
        $client = $this->createV2Client();
        $cycle = $this->cycle();

        $crawler = $client->request('GET', sprintf('/hamburg/cycles/%d/edit', $cycle->getId()));

        $this->assertV2('form.cm-form [data-controller="map--form-map"]');
        self::assertSelectorExists('form.cm-form select[name="city_cycle[dayOfWeek]"]');
        self::assertSelectorExists('form.cm-form input[name="city_cycle[_token]"]');
        self::assertSame($cycle->getTime()?->format('H:i'), $this->timeValue($crawler));
    }

    private function timeValue(Crawler $crawler): ?string
    {
        $field = $crawler->filter('[name="city_cycle[time]"]');

        return $field->count() ? $field->attr('value') : null;
    }

    public function testCycleAdd(): void
    {
        $client = $this->createV2Client();

        $client->request('GET', '/hamburg/cycles/add');

        $this->assertV2('form.cm-form input[name="city_cycle[location]"]');
        self::assertSelectorTextContains('h1', 'Turnus hinzufügen');
    }

    public function testCycleExecuteForm(): void
    {
        $client = $this->createV2Client();
        $cycle = $this->cycle();

        $client->request('GET', sprintf('/hamburg/cycles/%d/execute', $cycle->getId()));

        $this->assertV2('form.cm-form input[name$="[fromDate]"]');
        self::assertSelectorExists('form.cm-form button[name$="[submit]"]');
    }

    public function testCycleExecutePreview(): void
    {
        static::createClient();
        $cycle = $this->cycle();

        $ride = (new Ride())
            ->setTitle('Critical Mass Hamburg Dezember 2026')
            ->setDateTime(new \DateTime('2026-12-25 18:00:00', new \DateTimeZone('UTC')))
            ->setLocation('Moorweide');

        $executeable = (new CycleExecutable())
            ->setCityCycle($cycle)
            ->setFromDate(new \DateTime('2026-12-01'))
            ->setUntilDate(new \DateTime('2026-12-31'));

        $crawler = $this->renderV2('CityCycle/execute_preview.html.twig', [
            'cityCycle' => $cycle,
            'executeable' => $executeable,
            'dateTimeList' => [],
            'rideList' => [$ride],
        ]);

        self::assertCount(1, $crawler->filter('body.cm-v2'));
        self::assertStringContainsString('19:00 Uhr', $crawler->filter('.cm-tbl')->text());
        self::assertCount(1, $crawler->filter('form.cm-adm-foot input[name="fromDate"]'));
    }

    public function testSocialNetworkPages(): void
    {
        $client = $this->createV2Client();
        $profile = $this->em()->getRepository(SocialNetworkProfile::class)->findOneBy(['city' => $this->hamburg(), 'enabled' => true]);
        self::assertInstanceOf(SocialNetworkProfile::class, $profile);

        $client->request('GET', '/hamburg/socialnetwork/list');
        $this->assertV2('form#profile-add input[name$="[identifier]"]');
        self::assertSelectorExists(sprintf('form[action="/socialnetwork/%d/disable"] input[name="_token"]', $profile->getId()));

        $client->request('GET', '/hamburg/socialnetwork/add');
        $this->assertV2('form.cm-form input[name$="[identifier]"]');

        $client->request('GET', sprintf('/socialnetwork/%d/edit', $profile->getId()));
        $this->assertV2('form.cm-form select[name$="[network]"]');
    }

    public function testLocation(): void
    {
        $client = $this->createV2Client(false);
        $location = $this->em()->getRepository(Location::class)->findOneBy(['city' => $this->hamburg(), 'slug' => 'moorweide']);
        self::assertInstanceOf(Location::class, $location);

        $client->request('GET', '/hamburg/location/moorweide');

        $this->assertV2('[data-controller="map--map"]');
        self::assertSelectorTextContains('h1', (string) $location->getTitle());
    }

    public function testPromotion(): void
    {
        $client = $this->createV2Client(false);
        $em = $this->em();

        $promotion = (new Promotion())
            ->setSlug('v2-test-aktion')
            ->setTitle('Aktion im Test')
            ->setQuery('year=2026')
            ->setShowMap(true)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());
        $em->persist($promotion);
        $em->flush();

        try {
            $client->request('GET', '/promotion/v2-test-aktion');

            $this->assertV2('table#ride-table[data-controller="datatable"]');
        } finally {
            $promotion = $em->getRepository(Promotion::class)->findOneBy(['slug' => 'v2-test-aktion']);
            if ($promotion) {
                $em->remove($promotion);
                $em->flush();
            }
        }
    }

    public function testOAuthConsent(): void
    {
        static::createClient();

        $crawler = $this->renderV2('OAuth2/consent.html.twig', [
            'client' => new Client('Testanwendung', 'test-client', null),
            'scopes' => [new Scope('ride:read'), new Scope('track:write')],
            'authorize_url' => 'https://criticalmass.in/authorize?client_id=test-client',
        ]);

        self::assertCount(1, $crawler->filter('body.cm-v2'));
        self::assertCount(1, $crawler->filter('form[action="/oauth2/consent"] input[name="client_id"][value="test-client"]'));
        self::assertCount(1, $crawler->filter('button[name="consent"][value="approve"]'));
        self::assertStringContainsString('Tracks erstellen und bearbeiten', $crawler->filter('.cm-consent')->text());
    }

    /**
     * Rendert ohne Controller, darum eine Anfrage mit Cookie vorschieben.
     *
     * @param array<string, mixed> $context
     */
    private function renderV2(string $template, array $context): Crawler
    {
        $container = static::getContainer();
        $request = Request::create('/');
        $request->cookies->set(DesignChoice::COOKIE, DesignChoice::V2);
        $request->setSession(new Session(new MockArraySessionStorage()));

        /** @var RequestStack $requestStack */
        $requestStack = $container->get('request_stack');
        $requestStack->push($request);

        try {
            /** @var Environment $twig */
            $twig = $container->get('twig');

            return new Crawler($twig->render($template, $context));
        } finally {
            $requestStack->pop();
        }
    }
}
