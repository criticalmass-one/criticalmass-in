<?php declare(strict_types=1);

namespace Tests\Controller\V2;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Design\DesignChoice;
use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Ride;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Tests\Controller\AbstractControllerTestCase;

class RideCityTest extends AbstractControllerTestCase
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

    private function client(): KernelBrowser
    {
        $client = static::createClient();
        $client->getCookieJar()->set(new Cookie(DesignChoice::COOKIE, DesignChoice::V2));

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

    private function pastRide(): Ride
    {
        $ride = $this->entityManager()->getRepository(Ride::class)->createQueryBuilder('r')
            ->where('r.dateTime < :now')
            ->andWhere('r.enabled = true')
            ->setParameter('now', new \DateTime())
            ->orderBy('r.dateTime', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        self::assertInstanceOf(Ride::class, $ride);

        return $ride;
    }

    private function hamburg(): City
    {
        $city = $this->entityManager()->getRepository(City::class)->findOneBy(['city' => 'Hamburg']);
        self::assertInstanceOf(City::class, $city);

        return $city;
    }

    public function testRidePage(): void
    {
        $client = $this->client();
        $client->request('GET', $this->path($this->pastRide()));

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorExists('.cm-ride-head h1');
        self::assertSelectorExists('[data-controller="map--ride-map"]');
        self::assertSelectorExists('#tracks');
        self::assertSelectorExists('#comments');
        self::assertSelectorNotExists('#estimate-modal');
    }

    public function testRidePageForLoggedInUser(): void
    {
        $client = $this->client();
        $this->loginAs($client, 'cyclist@criticalmass.in');
        $client->request('GET', $this->path($this->pastRide()));

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorExists('#estimate-modal form');
        self::assertSelectorExists('#disable-modal form');
        self::assertSelectorExists('#modal-add-post');
        self::assertSelectorExists('.cm-ride-head form[action*="participation"]');
    }

    public function testCityPages(): void
    {
        $client = $this->client();
        $city = $this->hamburg();

        foreach ([
            'caldera_criticalmass_city_show',
            'caldera_criticalmass_city_listrides',
            'caldera_criticalmass_statistic_city',
            'caldera_criticalmass_city_missingstats',
            'caldera_criticalmass_city_listgalleries',
            'caldera_criticalmass_city_listtracks',
        ] as $route) {
            $client->request('GET', $this->path($city, $route));

            self::assertResponseIsSuccessful($route);
            self::assertSelectorExists('body.cm-v2');
            self::assertSelectorExists('nav.cm-tabs a.is-active');
        }

        $client->request('GET', $this->path($city));
        self::assertSelectorTextContains('h1', (string) $city->getTitle());
    }

    public function testRideGalleryAndPhoto(): void
    {
        $client = $this->client();
        $photo = $this->entityManager()->getRepository(Photo::class)->findOneBy(['enabled' => true, 'deleted' => false]);
        self::assertInstanceOf(Photo::class, $photo);
        $ride = $photo->getRide();
        self::assertInstanceOf(Ride::class, $ride);

        $client->request('GET', $this->path($ride, 'caldera_criticalmass_photo_ride_list'));
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorExists('.cm-masonry figure');

        $client->request('GET', $this->path($photo));
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorExists('.cm-photo-view img#photo');
    }
}
