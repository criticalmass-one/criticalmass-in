<?php declare(strict_types=1);

namespace Tests\Router;

use App\Criticalmass\Router\ObjectRouter;
use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Ride;
use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;

class RouterTest extends TestCase
{
    public function test1(): void
    {
        $route = new Route('caldera_criticalmass_city_show');

        $routeCollection = $this->createMock(RouteCollection::class);
        $routeCollection->method('get')->willReturn($route);

        $requestContext = $this->createMock(RequestContext::class);
        $urlGenerator = new UrlGenerator($routeCollection, $requestContext);

        $router = $this->createMock(Router::class);
        $router->method('getRouteCollection')->willReturn($routeCollection);
        $router->method('getGenerator')->willReturn($urlGenerator);

        $annotationReader = new AnnotationReader();
        $objectRouter = new ObjectRouter($router, $annotationReader);

        $citySlug = new CitySlug();
        $city = new City();

        $citySlug
            ->setSlug('testcity')
            ->setCity($city);

        $city->setMainSlug($citySlug);

        $route = $objectRouter->generate($city, 'caldera_criticalmass_city_show');

        $this->assertEquals('/testcity', $route);
    }

    public function testCity(): void
    {
        $citySlug = new CitySlug();
        $city = new City();

        $citySlug
            ->setSlug('testcity')
            ->setCity($city);

        $city->setMainSlug($citySlug);

        $route = $this->getRouter()->generate($city);

        $this->assertEquals('/testcity', $route);
    }

    public function testRide(): void
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

        $route = $this->getRouter()->generate($ride);

        $this->assertEquals('/testcity/2018-01-01', $route);
    }
}
