<?php declare(strict_types=1);

namespace Tests\Router;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Board;
use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Photo;
use App\Entity\Region;
use App\Entity\Ride;
use App\Entity\Thread;
use App\Entity\Track;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tests\Support\EntityIdHelper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RouterTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    protected function getObjectRouter(): ObjectRouterInterface
    {
        return static::getContainer()->get(ObjectRouterInterface::class);
    }

    public function testCityRoute(): void
    {
        $citySlug = new CitySlug();
        $city = new City();

        $citySlug
            ->setSlug('testcity')
            ->setCity($city);

        $city->setMainSlug($citySlug);

        $route = $this->getObjectRouter()->generate($city);

        $this->assertEquals('/testcity', $route);
    }

    public function testRideRoute(): void
    {
        $citySlug = new CitySlug();
        $city = new City();

        $citySlug
            ->setSlug('testcity')
            ->setCity($city);

        $city->setMainSlug($citySlug);

        $ride = new Ride();
        $ride
            ->setDateTime(new \DateTime('2018-01-01'))
            ->setCity($city);

        $city->addRide($ride);

        $route = $this->getObjectRouter()->generate($ride);

        $this->assertEquals('/testcity/2018-01-01', $route);
    }

    public function testCityRouteWithExplicitRouteName(): void
    {
        $citySlug = new CitySlug();
        $city = new City();

        $citySlug
            ->setSlug('hamburg')
            ->setCity($city);

        $city->setMainSlug($citySlug);

        $route = $this->getObjectRouter()->generate($city, 'caldera_criticalmass_city_show');

        $this->assertEquals('/hamburg', $route);
    }

    private function city(string $slug): City
    {
        $city = new City();
        $city->setMainSlug((new CitySlug())->setSlug($slug)->setCity($city));

        return $city;
    }

    public function testRideWithSlugUsesTheSlug(): void
    {
        $ride = (new Ride())->setCity($this->city('hamburg'))->setDateTime(new \DateTime('2024-05-31'))->setSlug('kidical-mass');

        $this->assertEquals('/hamburg/kidical-mass', $this->getObjectRouter()->generate($ride));
    }

    public function testAbsoluteUrlUsesConfiguredHost(): void
    {
        $route = $this->getObjectRouter()->generate($this->city('hamburg'), null, [], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->assertStringEndsWith('/hamburg', $route);
        $this->assertMatchesRegularExpression('#^https?://#', $route);
    }

    public function testPhotoRoute(): void
    {
        $city = $this->city('hamburg');
        $ride = (new Ride())->setCity($city)->setDateTime(new \DateTime('2024-05-31'));
        $photo = (new Photo())->setRide($ride)->setCity($city);
        EntityIdHelper::setId($photo, 4711);

        $this->assertEquals('/hamburg/2024-05-31/photo/4711', $this->getObjectRouter()->generate($photo));
    }

    public function testTrackRoute(): void
    {
        $track = new Track();
        EntityIdHelper::setId($track, 12);

        $this->assertEquals('/track/view/12', $this->getObjectRouter()->generate($track));
    }

    public function testCityThreadAndBoardThreadRoutes(): void
    {
        $cityThread = (new Thread())->setSlug('treffpunkt')->setCity($this->city('hamburg'));
        $boardThread = (new Thread())->setSlug('regeln')->setBoard((new Board())->setSlug('allgemein'));

        $this->assertEquals('/hamburg/thread/treffpunkt', $this->getObjectRouter()->generate($cityThread));
        $this->assertEquals('/boards/allgemein/thread/regeln', $this->getObjectRouter()->generate($boardThread));
    }

    public function testBoardRoute(): void
    {
        $this->assertEquals('/boards/allgemein', $this->getObjectRouter()->generate((new Board())->setSlug('allgemein')));
    }

    public function testRegionRoutesFollowTheHierarchy(): void
    {
        $world = (new Region())->setSlug('world');
        $europe = (new Region())->setSlug('europe')->setParent($world);
        $germany = (new Region())->setSlug('germany')->setParent($europe);
        $north = (new Region())->setSlug('north')->setParent($germany);

        $router = $this->getObjectRouter();

        $this->assertEquals('/world', $router->generate($world));
        $this->assertEquals('/world/europe', $router->generate($europe));
        $this->assertEquals('/world/europe/germany', $router->generate($germany));
        $this->assertEquals('/world/europe/germany/north', $router->generate($north));
    }
}
