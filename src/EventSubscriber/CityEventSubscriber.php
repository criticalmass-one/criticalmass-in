<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Criticalmass\Wikidata\CityTimezoneDetector\CityTimezoneDetectorInterface;
use App\Event\City\CityCreatedEvent;
use App\Event\City\CityUpdatedEvent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CityEventSubscriber implements EventSubscriberInterface
{
    /** @var CityTimezoneDetectorInterface $cityTimezoneDetector */
    protected $cityTimezoneDetector;

    /** @var ManagerRegistry $registry; */
    protected $registry;

    public function __construct(CityTimezoneDetectorInterface $cityTimezoneDetector, ManagerRegistry $registry)
    {
        $this->cityTimezoneDetector = $cityTimezoneDetector;
        $this->registry = $registry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CityCreatedEvent::NAME => 'onCityCreated',
            CityUpdatedEvent::NAME => 'onCityUpdated',
        ];
    }

    public function onCityCreated(CityCreatedEvent $cityCreatedEvent): void
    {
        $city = $cityCreatedEvent->getCity();

        if ($timezone = $this->cityTimezoneDetector->queryForCity($city)) {
            $city->setTimezone($timezone);

            $this->registry->getManager()->flush();
        }
    }

    public function onCityUpdated(CityUpdatedEvent $cityUpdatedEvent): void
    {
        
    }
}
