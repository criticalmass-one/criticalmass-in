<?php declare(strict_types=1);

namespace Tests\Router;

use App\Criticalmass\Router\DelegatedRouter\BoardRouter;
use App\Criticalmass\Router\DelegatedRouter\RegionRouter;
use App\Criticalmass\Router\DelegatedRouter\RideRouter;
use App\Criticalmass\Router\DelegatedRouter\ThreadRouter;
use App\Criticalmass\Router\DelegatedRouterManager\DelegatedRouterManager;
use App\Criticalmass\Router\ObjectRouter;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\Router\ParameterResolver\ClassParameterResolver;
use App\Criticalmass\Router\ParameterResolver\PropertyParameterResolver;
use App\Entity\Board;
use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Photo;
use App\Entity\Region;
use App\Entity\Ride;
use App\Entity\Thread;
use App\Entity\Track;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tests\Support\EntityIdHelper;
use Tests\Support\StubRouter;

final class DelegatedRouterTest extends TestCase
{
    private StubRouter $router;
    private ClassParameterResolver $classResolver;
    private PropertyParameterResolver $propertyResolver;
    private DelegatedRouterManager $manager;
    private ObjectRouter $objectRouter;

    protected function setUp(): void
    {
        $this->router = new StubRouter([
            'caldera_criticalmass_city_show' => '/{citySlug}',
            'caldera_criticalmass_ride_show' => '/{citySlug}/{rideIdentifier}',
            'caldera_criticalmass_photo_show_ride' => '/{citySlug}/{rideIdentifier}/photo/{id}',
            'caldera_criticalmass_track_view' => '/track/{id}',
            'caldera_criticalmass_board_listthreads' => '/boards/{boardSlug}',
            'caldera_criticalmass_board_listcitythreads' => '/{citySlug}/listthreads',
            'caldera_criticalmass_board_viewthread' => '/boards/{boardSlug}/thread/{threadSlug}',
            'caldera_criticalmass_board_viewcitythread' => '/{citySlug}/thread/{threadSlug}',
            'caldera_criticalmass_region_world' => '/world',
            'caldera_criticalmass_region_world_region_1' => '/world/{slug1}',
            'caldera_criticalmass_region_world_region_2' => '/world/{slug1}/{slug2}',
            'caldera_criticalmass_region_world_region_3' => '/world/{slug1}/{slug2}/{slug3}',
        ]);

        $this->manager = new DelegatedRouterManager();
        $this->classResolver = new ClassParameterResolver(new ParameterBag());
        $this->propertyResolver = new PropertyParameterResolver($this->manager, $this->classResolver);

        foreach ([RideRouter::class, ThreadRouter::class, BoardRouter::class, RegionRouter::class] as $routerClass) {
            $this->manager->addDelegatedRouter(new $routerClass($this->router, $this->classResolver, $this->propertyResolver));
        }

        $this->objectRouter = new ObjectRouter($this->router, $this->classResolver, $this->propertyResolver, $this->manager);
    }

    private function city(string $slug): City
    {
        $city = new City();
        $city->setMainSlug((new CitySlug())->setSlug($slug)->setCity($city));

        return $city;
    }

    #[Test]
    public function cityUsesItsDefaultRouteAndMainSlug(): void
    {
        self::assertSame('/hamburg', $this->objectRouter->generate($this->city('hamburg')));
        self::assertSame('https://criticalmass.in/hamburg', $this->objectRouter->generate($this->city('hamburg'), null, [], UrlGeneratorInterface::ABSOLUTE_URL));
    }

    #[Test]
    public function rideWithoutSlugIsAddressedByDate(): void
    {
        $ride = (new Ride())->setCity($this->city('hamburg'))->setDateTime(new \DateTime('2024-05-31 19:00:00'));

        self::assertSame('/hamburg/2024-05-31', $this->objectRouter->generate($ride));
    }

    #[Test]
    public function rideWithSlugPrefersTheSlug(): void
    {
        $ride = (new Ride())->setCity($this->city('hamburg'))->setDateTime(new \DateTime('2024-05-31'))->setSlug('kidical-mass');

        self::assertSame('/hamburg/kidical-mass', $this->objectRouter->generate($ride));
    }

    #[Test]
    public function rideRouterResolvesRideIdentifierAndFallsBackForOtherParameters(): void
    {
        $rideRouter = new RideRouter($this->router, $this->classResolver, $this->propertyResolver);
        $ride = (new Ride())->setCity($this->city('berlin'))->setDateTime(new \DateTime('2024-05-31'));

        self::assertSame('2024-05-31', $rideRouter->getRouteParameter($ride, 'rideIdentifier'));
        self::assertSame('berlin', $rideRouter->getRouteParameter($ride, 'citySlug'));
        self::assertNull($rideRouter->getRouteParameter($ride, 'unknown'));
    }

    #[Test]
    public function extraParametersAreAppendedAsQueryString(): void
    {
        self::assertSame('/hamburg?page=2', $this->objectRouter->generate($this->city('hamburg'), null, ['page' => 2]));
    }

    #[Test]
    public function photoRouteCombinesCityRideAndId(): void
    {
        $city = $this->city('hamburg');
        $ride = (new Ride())->setCity($city)->setDateTime(new \DateTime('2024-05-31'));
        $photo = (new Photo())->setRide($ride)->setCity($city);
        EntityIdHelper::setId($photo, 77);

        self::assertSame('/hamburg/2024-05-31/photo/77', $this->objectRouter->generate($photo));
    }

    #[Test]
    public function trackRouteUsesItsId(): void
    {
        $track = new Track();
        EntityIdHelper::setId($track, 12);

        self::assertSame('/track/12', $this->objectRouter->generate($track));
    }

    #[Test]
    public function boardThreadsAndCityThreadsHaveDifferentRoutes(): void
    {
        $board = (new Board())->setSlug('general');

        self::assertSame('/boards/general', $this->objectRouter->generate($board));

        $boardRouter = new BoardRouter($this->router, $this->classResolver, $this->propertyResolver);
        self::assertSame('/hamburg/listthreads', $boardRouter->generate($this->city('hamburg')));
    }

    #[Test]
    public function threadRouteDependsOnWhetherItBelongsToACity(): void
    {
        $cityThread = (new Thread())->setSlug('meeting-point')->setCity($this->city('hamburg'));
        $boardThread = (new Thread())->setSlug('rules')->setBoard((new Board())->setSlug('general'));

        self::assertSame('/hamburg/thread/meeting-point', $this->objectRouter->generate($cityThread));
        self::assertSame('/boards/general/thread/rules', $this->objectRouter->generate($boardThread));
    }

    #[Test]
    public function regionRouteReflectsTheHierarchyDepth(): void
    {
        $world = (new Region())->setSlug('world');
        $europe = (new Region())->setSlug('europe')->setParent($world);
        $germany = (new Region())->setSlug('germany')->setParent($europe);
        $north = (new Region())->setSlug('north')->setParent($germany);
        $tooDeep = (new Region())->setSlug('deep')->setParent($north);

        self::assertSame('/world', $this->objectRouter->generate($world));
        self::assertSame('/world/europe', $this->objectRouter->generate($europe));
        self::assertSame('/world/europe/germany', $this->objectRouter->generate($germany));
        self::assertSame('/world/europe/germany/north', $this->objectRouter->generate($north));
        self::assertSame('', $this->objectRouter->generate($tooDeep));
    }

    #[Test]
    public function managerReturnsTheFirstSupportingRouterWiredToTheObjectRouter(): void
    {
        $manager = new DelegatedRouterManager();
        $rideRouter = new RideRouter($this->router, $this->classResolver, $this->propertyResolver);
        $manager->addDelegatedRouter($rideRouter);

        $objectRouter = $this->createMock(ObjectRouterInterface::class);
        $manager->setObjectRouter($objectRouter);

        self::assertSame($rideRouter, $manager->findDelegatedRouter(new Ride()));
        self::assertNull($manager->findDelegatedRouter(new City()));
    }

    #[Test]
    public function delegatedRouterSupportIsDerivedFromItsClassName(): void
    {
        $rideRouter = new RideRouter($this->router, $this->classResolver, $this->propertyResolver);
        $threadRouter = new ThreadRouter($this->router, $this->classResolver, $this->propertyResolver);

        self::assertTrue($rideRouter->supports(new Ride()));
        self::assertFalse($rideRouter->supports(new Thread()));
        self::assertTrue($threadRouter->supports(new Thread()));
        self::assertFalse($threadRouter->supports(new Ride()));
    }

    #[Test]
    public function unknownRouteNameThrows(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Route does_not_exist not found');

        $this->objectRouter->generate($this->city('hamburg'), 'does_not_exist');
    }

    #[Test]
    public function missingMandatoryParameterYieldsEmptyStringInsteadOfException(): void
    {
        // Ride without city → citySlug cannot be resolved → InvalidParameterException is swallowed.
        $ride = (new Ride())->setDateTime(new \DateTime('2024-05-31'));

        self::assertSame('', $this->objectRouter->generate($ride));
    }
}
