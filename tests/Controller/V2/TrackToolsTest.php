<?php declare(strict_types=1);

namespace Tests\Controller\V2;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Design\DesignChoice;
use App\Entity\City;
use App\Entity\Ride;
use App\Entity\Subride;
use App\Entity\Track;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DomCrawler\Field\FileFormField;
use Tests\Controller\AbstractControllerTestCase;

class TrackToolsTest extends AbstractControllerTestCase
{
    private const string USER = 'testuser@criticalmass.in';

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

    private function client(bool $login = true): KernelBrowser
    {
        $client = static::createClient();
        $client->getCookieJar()->set(new Cookie(DesignChoice::COOKIE, DesignChoice::V2));

        if ($login) {
            $this->loginAs($client, self::USER);
        }

        return $client;
    }

    private function entityManager(): EntityManagerInterface
    {
        return static::getContainer()->get('doctrine')->getManager();
    }

    private function path(object $entity, ?string $route = null): string
    {
        /** @var ObjectRouterInterface $router */
        $router = static::getContainer()->get(ObjectRouterInterface::class);

        return $router->generate($entity, $route);
    }

    private function hamburgRide(): Ride
    {
        $city = $this->entityManager()->getRepository(City::class)->findOneBy(['city' => 'Hamburg']);
        self::assertInstanceOf(City::class, $city);

        $ride = $this->entityManager()->getRepository(Ride::class)->createQueryBuilder('r')
            ->where('r.city = :city')
            ->andWhere('r.dateTime < :now')
            ->andWhere('r.slug IS NULL')
            ->setParameter('city', $city)
            ->setParameter('now', new \DateTime())
            ->orderBy('r.dateTime', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        self::assertInstanceOf(Ride::class, $ride);

        return $ride;
    }

    private function assertV2Page(string $heading): void
    {
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorTextContains('h1', $heading);
    }

    public function testTrackUploadViewRangeAndTime(): void
    {
        $client = $this->client();
        $ride = $this->hamburgRide();

        $crawler = $client->request('GET', $this->path($ride, 'caldera_criticalmass_track_upload'));
        $this->assertV2Page('Track hochladen');
        self::assertSelectorExists('form#track-upload-form.cm-form input[type=file][accept=".gpx,.fit"]');

        $gpx = sys_get_temp_dir() . '/v2-track-tools-' . uniqid() . '.gpx';
        copy(__DIR__ . '/../../PhotoGps/data/braunschweig.gpx', $gpx);

        $form = $crawler->filter('form#track-upload-form')->form();
        $fileField = $form['form[trackFile][file]'];
        self::assertInstanceOf(FileFormField::class, $fileField);
        $fileField->upload($gpx);
        $client->submit($form);
        @unlink($gpx);

        self::assertResponseRedirects();
        $client->followRedirect();
        $this->assertV2Page($ride->getTitle());
        self::assertSelectorExists('[data-controller="map--map"]');

        $trackId = (int) preg_replace('#.*/track/view/(\d+).*#', '$1', (string) $client->getRequest()->getUri());
        $track = $this->entityManager()->getRepository(Track::class)->find($trackId);
        self::assertInstanceOf(Track::class, $track);

        try {
            if (!$track->isReviewed()) {
                self::assertSelectorExists('.cm-tt-review a[href$="/approve"]');
            }

            $client->request('GET', $this->path($track, 'caldera_criticalmass_track_range'));
            $this->assertV2Page('Track beschneiden');
            self::assertSelectorExists('[data-controller="map--track-range-map"]');
            self::assertSelectorExists('#slider');
            self::assertSelectorExists('#track_range_latLngList');
            self::assertSelectorExists('#track_range_startPoint');

            $client->request('GET', $this->path($track, 'caldera_criticalmass_track_time'));
            $this->assertV2Page('Zeit anpassen');
            self::assertSelectorExists('input[name="form[startDate]"]');
            self::assertSelectorExists('input[name="form[startTime]"]');
        } finally {
            $em = $this->entityManager();
            $em->remove($em->getRepository(Track::class)->find($trackId));
            $em->flush();
        }
    }

    public function testStravaAuth(): void
    {
        $client = $this->client();
        $client->request('GET', $this->path($this->hamburgRide(), 'caldera_criticalmass_strava_auth'));

        $this->assertV2Page('Track von Strava importieren');
        self::assertSelectorExists('a.cm-btn-go[href*="strava.com"]');
    }

    public function testSubridePages(): void
    {
        $client = $this->client();
        $ride = $this->hamburgRide();

        $client->request('GET', $this->path($ride, 'caldera_criticalmass_subride_add'));
        $this->assertV2Page('Mini-Mass eintragen');
        self::assertSelectorExists('[data-controller="map--form-map"]');
        self::assertSelectorExists('input[name="subride[title]"]');

        $client->request('GET', $this->path($ride, 'caldera_criticalmass_subride_preparecopy'));
        $this->assertV2Page('Mini-Masses kopieren');

        $em = $this->entityManager();
        $ride = $em->getRepository(Ride::class)->find($ride->getId());
        $subride = (new Subride())
            ->setRide($ride)
            ->setUser($this->getUser(self::USER))
            ->setDateTime($ride->getDateTime())
            ->setTitle('Zubringer V2-Test')
            ->setLocation('Bahnhof')
            ->setLatitude(53.55)
            ->setLongitude(9.93);
        $em->persist($subride);
        $em->flush();

        try {
            $client->request('GET', $this->path($subride, 'caldera_criticalmass_subride_edit'));
            $this->assertV2Page('Mini-Mass bearbeiten');
            self::assertInputValueSame('subride[title]', 'Zubringer V2-Test');
        } finally {
            $em = $this->entityManager();
            $em->remove($em->getRepository(Subride::class)->find($subride->getId()));
            $em->flush();
        }
    }

    public function testSocialPreview(): void
    {
        $client = $this->client();
        $client->request('GET', $this->path($this->hamburgRide(), 'caldera_criticalmass_ride_socialpreview'));

        $this->assertV2Page('Social-Media-Vorschau');
        self::assertSelectorExists('form.cm-form textarea');
    }

    public function testAnonymousEstimate(): void
    {
        $client = $this->client(false);
        $client->request('GET', $this->path($this->hamburgRide(), 'caldera_criticalmass_ride_addestimate_anonymous'));

        $this->assertV2Page('Teilnehmerzahl ergänzen');
        self::assertSelectorExists('.cm-solo input[name="ride_estimate[estimatedParticipants]"]');
    }
}
