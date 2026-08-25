<?php declare(strict_types=1);

namespace Tests\EventSubscriber;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\City;
use App\Entity\Ride;
use App\EventSubscriber\SitemapEventSubscriber;
use App\Repository\CityRepository;
use App\Repository\RideRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SitemapEventSubscriberTest extends TestCase
{
    #[Test]
    public function registersEveryRideAndCityInItsOwnSection(): void
    {
        $city = new City();
        $ride = new Ride();

        $cityRepository = $this->createMock(CityRepository::class);
        $cityRepository->method('findCities')->willReturn([$city]);

        $rideRepository = $this->createMock(RideRepository::class);
        $rideRepository->method('findRides')->willReturn([$ride]);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->willReturnMap([
            [City::class, null, $cityRepository],
            [Ride::class, null, $rideRepository],
        ]);

        $router = $this->createMock(ObjectRouterInterface::class);
        $router->method('generate')->willReturnCallback(
            static fn (object $object): string => $object instanceof Ride ? 'https://criticalmass.in/hamburg/2024-05-31' : 'https://criticalmass.in/hamburg'
        );

        $added = [];
        $urlContainer = $this->createMock(UrlContainerInterface::class);
        $urlContainer->method('addUrl')->willReturnCallback(function (UrlConcrete $url, string $section) use (&$added): void {
            $added[] = [$section, $url->getLoc()];
        });

        $event = new SitemapPopulateEvent($urlContainer, $this->createMock(UrlGeneratorInterface::class));

        (new SitemapEventSubscriber($router, $registry))->populate($event);

        self::assertSame([
            ['ride', 'https://criticalmass.in/hamburg/2024-05-31'],
            ['city', 'https://criticalmass.in/hamburg'],
        ], $added);
    }

    #[Test]
    public function subscribesToSitemapPopulateEvent(): void
    {
        self::assertSame([SitemapPopulateEvent::class => 'populate'], SitemapEventSubscriber::getSubscribedEvents());
    }
}
