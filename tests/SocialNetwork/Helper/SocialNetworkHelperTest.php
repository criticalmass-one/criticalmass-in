<?php declare(strict_types=1);

namespace Tests\SocialNetwork\Helper;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\SocialNetwork\Helper\SocialNetworkHelper;
use App\Entity\City;
use App\Entity\Ride;
use App\Entity\SocialNetworkProfile;
use App\Entity\Subride;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class SocialNetworkHelperTest extends TestCase
{
    public function testGetProfileAbleCity(): void
    {
        $registry = $this->createMock(RegistryInterface::class);
        $objectRouter = $this->createMock(ObjectRouterInterface::class);

        $socialNetworkHelper = new SocialNetworkHelper($registry, $objectRouter);

        $city = new City();
        $socialNetworkProfileCity = (new SocialNetworkProfile())->setCity($city);
        $this->assertEquals($city, $socialNetworkHelper->getProfileAble($socialNetworkProfileCity));
    }

    public function testGetProfileAbleRide(): void
    {
        $registry = $this->createMock(RegistryInterface::class);
        $objectRouter = $this->createMock(ObjectRouterInterface::class);

        $socialNetworkHelper = new SocialNetworkHelper($registry, $objectRouter);

        $ride = new Ride();
        $socialNetworkProfileRide = (new SocialNetworkProfile())->setRide($ride);
        $this->assertEquals($ride, $socialNetworkHelper->getProfileAble($socialNetworkProfileRide));
    }


    public function testGetProfileAbleSubride(): void
    {
        $registry = $this->createMock(RegistryInterface::class);
        $objectRouter = $this->createMock(ObjectRouterInterface::class);

        $socialNetworkHelper = new SocialNetworkHelper($registry, $objectRouter);

        $subride = new Subride();
        $socialNetworkProfileSubride = (new SocialNetworkProfile())->setSubride($subride);
        $this->assertEquals($subride, $socialNetworkHelper->getProfileAble($socialNetworkProfileSubride));
    }

    public function testGetProfileAbleUser(): void
    {
        $registry = $this->createMock(RegistryInterface::class);
        $objectRouter = $this->createMock(ObjectRouterInterface::class);

        $socialNetworkHelper = new SocialNetworkHelper($registry, $objectRouter);

        $user = new User();
        $socialNetworkProfileUser = (new SocialNetworkProfile())->setUser($user);
        $this->assertEquals($user, $socialNetworkHelper->getProfileAble($socialNetworkProfileUser));
    }

    public function testGetProfileAbleNull(): void
    {
        $registry = $this->createMock(RegistryInterface::class);
        $objectRouter = $this->createMock(ObjectRouterInterface::class);

        $socialNetworkHelper = new SocialNetworkHelper($registry, $objectRouter);

        $socialNetworkProfileEmpty = new SocialNetworkProfile();
        $this->assertNull($socialNetworkHelper->getProfileAble($socialNetworkProfileEmpty));
    }

    public function testGetProfileAbleShortname(): void
    {
        $registry = $this->createMock(RegistryInterface::class);
        $objectRouter = $this->createMock(ObjectRouterInterface::class);

        $socialNetworkHelper = new SocialNetworkHelper($registry, $objectRouter);

        $this->assertEquals('User', $socialNetworkHelper->getProfileAbleShortname(new User()));
        $this->assertEquals('City', $socialNetworkHelper->getProfileAbleShortname(new City()));
        $this->assertEquals('Ride', $socialNetworkHelper->getProfileAbleShortname(new Ride()));
        $this->assertEquals('Subride', $socialNetworkHelper->getProfileAbleShortname(new Subride()));
    }

    public function testAssignProfileAbleCity(): void
    {
        $registry = $this->createMock(RegistryInterface::class);
        $objectRouter = $this->createMock(ObjectRouterInterface::class);

        $socialNetworkHelper = new SocialNetworkHelper($registry, $objectRouter);

        $city = new City();
        $request = new Request(['city' => $city]);

        $socialNetworkProfile = new SocialNetworkProfile();
        $socialNetworkHelper->assignProfileAble($socialNetworkProfile, $request);

        $this->assertEquals($city, $socialNetworkProfile->getCity());
        $this->assertNull($socialNetworkProfile->getRide());
        $this->assertNull($socialNetworkProfile->getSubride());
        $this->assertNull($socialNetworkProfile->getUser());
    }

    public function testAssignProfileAbleRide(): void
    {
        $registry = $this->createMock(RegistryInterface::class);
        $objectRouter = $this->createMock(ObjectRouterInterface::class);

        $socialNetworkHelper = new SocialNetworkHelper($registry, $objectRouter);

        $ride = new Ride();
        $request = new Request(['ride' => $ride]);

        $socialNetworkProfile = new SocialNetworkProfile();
        $socialNetworkHelper->assignProfileAble($socialNetworkProfile, $request);

        $this->assertNull($socialNetworkProfile->getCity());
        $this->assertEquals($ride, $socialNetworkProfile->getRide());
        $this->assertNull($socialNetworkProfile->getSubride());
        $this->assertNull($socialNetworkProfile->getUser());
    }

    public function testAssignProfileAbleSubride(): void
    {
        $registry = $this->createMock(RegistryInterface::class);
        $objectRouter = $this->createMock(ObjectRouterInterface::class);

        $socialNetworkHelper = new SocialNetworkHelper($registry, $objectRouter);

        $subride = new Subride();
        $request = new Request(['subride' => $subride]);

        $socialNetworkProfile = new SocialNetworkProfile();
        $socialNetworkHelper->assignProfileAble($socialNetworkProfile, $request);

        $this->assertNull($socialNetworkProfile->getCity());
        $this->assertNull($socialNetworkProfile->getRide());
        $this->assertEquals($subride, $socialNetworkProfile->getSubride());
        $this->assertNull($socialNetworkProfile->getUser());
    }

    public function testAssignProfileAbleUser(): void
    {
        $registry = $this->createMock(RegistryInterface::class);
        $objectRouter = $this->createMock(ObjectRouterInterface::class);

        $socialNetworkHelper = new SocialNetworkHelper($registry, $objectRouter);

        $user = new User();
        $request = new Request(['user' => $user]);

        $socialNetworkProfile = new SocialNetworkProfile();
        $socialNetworkHelper->assignProfileAble($socialNetworkProfile, $request);

        $this->assertNull($socialNetworkProfile->getCity());
        $this->assertNull($socialNetworkProfile->getRide());
        $this->assertNull($socialNetworkProfile->getSubride());
        $this->assertEquals($user, $socialNetworkProfile->getUser());
    }

    public function testGetRouteNameCity(): void
    {
        $city = new City();

        $registry = $this->createMock(RegistryInterface::class);
        $objectRouter = $this->createMock(ObjectRouterInterface::class);
        $objectRouter
            ->expects($this->once())
            ->method('generate')
            ->with($this->equalTo($city), $this->equalTo('criticalmass_socialnetwork_city_test'));

        $socialNetworkHelper = new SocialNetworkHelper($registry, $objectRouter);
        $socialNetworkHelper->getRouteName($city, 'test');
    }

    public function testGetRouteNameRide(): void
    {
        $ride = new Ride();

        $registry = $this->createMock(RegistryInterface::class);
        $objectRouter = $this->createMock(ObjectRouterInterface::class);
        $objectRouter
            ->expects($this->once())
            ->method('generate')
            ->with($this->equalTo($ride), $this->equalTo('criticalmass_socialnetwork_ride_test'));

        $socialNetworkHelper = new SocialNetworkHelper($registry, $objectRouter);
        $socialNetworkHelper->getRouteName($ride, 'test');
    }

    public function testGetRouteNameSubride(): void
    {
        $subride = new Subride();

        $registry = $this->createMock(RegistryInterface::class);
        $objectRouter = $this->createMock(ObjectRouterInterface::class);
        $objectRouter
            ->expects($this->once())
            ->method('generate')
            ->with($this->equalTo($subride), $this->equalTo('criticalmass_socialnetwork_subride_test'));

        $socialNetworkHelper = new SocialNetworkHelper($registry, $objectRouter);
        $socialNetworkHelper->getRouteName($subride, 'test');
    }

    public function testGetRouteNameUser(): void
    {
        $user = new User();

        $registry = $this->createMock(RegistryInterface::class);
        $objectRouter = $this->createMock(ObjectRouterInterface::class);
        $objectRouter
            ->expects($this->once())
            ->method('generate')
            ->with($this->equalTo($user), $this->equalTo('criticalmass_socialnetwork_user_test'));

        $socialNetworkHelper = new SocialNetworkHelper($registry, $objectRouter);
        $socialNetworkHelper->getRouteName($user, 'test');
    }

    public function testGetProfileListCity(): void
    {
        $city = new City();

        $repository = $this->createMock(EntityRepository::class);
        $repository
            ->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('findByCity'), $this->equalTo([$city]))
            ->will($this->returnValue([]));

        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(SocialNetworkProfile::class))
            ->will($this->returnValue($repository));

        $objectRouter = $this->createMock(ObjectRouterInterface::class);

        $socialNetworkHelper = new SocialNetworkHelper($registry, $objectRouter);
        $socialNetworkHelper->getProfileList($city);
    }

    public function testGetProfileListRide(): void
    {
        $ride = new Ride();

        $repository = $this->createMock(EntityRepository::class);
        $repository
            ->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('findByRide'), $this->equalTo([$ride]))
            ->will($this->returnValue([]));

        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(SocialNetworkProfile::class))
            ->will($this->returnValue($repository));

        $objectRouter = $this->createMock(ObjectRouterInterface::class);

        $socialNetworkHelper = new SocialNetworkHelper($registry, $objectRouter);
        $socialNetworkHelper->getProfileList($ride);
    }

    public function testGetProfileListSubride(): void
    {
        $subride = new Subride();

        $repository = $this->createMock(EntityRepository::class);
        $repository
            ->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('findBySubride'), $this->equalTo([$subride]))
            ->will($this->returnValue([]));

        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(SocialNetworkProfile::class))
            ->will($this->returnValue($repository));

        $objectRouter = $this->createMock(ObjectRouterInterface::class);

        $socialNetworkHelper = new SocialNetworkHelper($registry, $objectRouter);
        $socialNetworkHelper->getProfileList($subride);
    }

    public function testGetProfileListUser(): void
    {
        $user = new User();

        $repository = $this->createMock(EntityRepository::class);
        $repository
            ->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('findByUser'), $this->equalTo([$user]))
            ->will($this->returnValue([]));

        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(SocialNetworkProfile::class))
            ->will($this->returnValue($repository));

        $objectRouter = $this->createMock(ObjectRouterInterface::class);

        $socialNetworkHelper = new SocialNetworkHelper($registry, $objectRouter);
        $socialNetworkHelper->getProfileList($user);
    }
}