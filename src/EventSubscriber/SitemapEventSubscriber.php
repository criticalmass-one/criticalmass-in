<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\City;
use App\Entity\Ride;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class SitemapEventSubscriber implements EventSubscriberInterface
{
    public function __construct(protected ObjectRouterInterface $router, protected ManagerRegistry $registry)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SitemapPopulateEvent::ON_SITEMAP_POPULATE => 'populate',
        ];
    }

    public function populate(SitemapPopulateEvent $event): void
    {
        $this->registerRideUrls($event->getUrlContainer());
        $this->registerCityUrls($event->getUrlContainer());
    }

    public function registerCityUrls(UrlContainerInterface $urlContainer): void
    {
        $cityList = $this->registry->getRepository(City::class)->findCities();

        /** @var City $city */
        foreach ($cityList as $city) {
            $url = $this->router->generate($city);

            $urlContainer->addUrl(new UrlConcrete($url), 'city');
        }
    }

    public function registerRideUrls(UrlContainerInterface $urlContainer): void
    {
        $rideList = $this->registry->getRepository(Ride::class)->findRides();

        /** @var Ride $ride */
        foreach ($rideList as $ride) {
            $url = $this->router->generate($ride);

            $urlContainer->addUrl(new UrlConcrete($url), 'ride');
        }
    }
}
