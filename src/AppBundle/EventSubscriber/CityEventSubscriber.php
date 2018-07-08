<?php declare(strict_types=1);

namespace AppBundle\EventSubscriber;

use AppBundle\Criticalmass\Timezone\CityTimezoneDetector\CityTimezoneDetectorInterface;
use AppBundle\Event\City\CityCreatedEvent;
use AppBundle\Event\City\CityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CityEventSubscriber implements EventSubscriberInterface
{
    /** @var CityTimezoneDetectorInterface $cityTimezoneDetector */
    protected $cityTimezoneDetector;

    public function __construct(CityTimezoneDetectorInterface $cityTimezoneDetector)
    {
        $this->cityTimezoneDetector = $cityTimezoneDetector;
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

        $this->cityTimezoneDetector->queryForCity($city);
    }

    public function onCityUpdated(CityUpdatedEvent $cityUpdatedEvent): void
    {
        $city = $cityUpdatedEvent->getCity();

        $this->cityTimezoneDetector->queryForCity($city);
    }
}
