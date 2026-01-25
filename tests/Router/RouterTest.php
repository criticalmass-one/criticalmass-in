<?php declare(strict_types=1);

namespace Tests\Router;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Ride;
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
}
