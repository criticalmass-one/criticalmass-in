<?php declare(strict_types=1);

namespace Criticalmass\Component\Facebook\Bridge;

use Criticalmass\Bundle\AppBundle\Entity\FacebookRideProperties;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Component\Facebook\Api\FacebookEventApi;
use Criticalmass\Component\Util\DateTimeUtil;
use Facebook\GraphNodes\GraphEdge;
use Facebook\GraphNodes\GraphEvent;
use Facebook\GraphNodes\GraphLocation;
use Facebook\GraphNodes\GraphPage;

class RideBridge extends AbstractBridge
{
    protected $facebookEventApi;

    public function __construct(FacebookEventApi $facebookEventApi)
    {
        $this->facebookEventApi = $facebookEventApi;
    }

    public function getEventPropertiesForRide(Ride $ride): ?FacebookRideProperties
    {
        $eventId = $this->getRideEventId($ride);

        if (!$eventId) {
            return null;
        }

        $event = $this->facebookEventApi->queryEvent($eventId);

        if ($event) {
            $properties = new FacebookRideProperties();

            $properties
                ->setRide($ride)
                ->setName($event->getName())
                ->setDescription($event->getField('description'))
                ->setStartTime($event->getField('start_time'))
                ->setEndTime($event->getField('end_time'))
                ->setUpdatedTime($event->getField('updated_time'))
                ->setNumberAttending($event->getField('attending_count'))
                ->setNumberDeclined($event->getField('declined_count'))
                ->setNumberInterested($event->getField('interested_count'))
                ->setNumberMaybe($event->getField('maybe_count'))
                ->setNumberNoreply($event->getField('noreply_count'))
            ;

            /** @var GraphPage $place */
            if ($place = $event->getPlace()) {
                $properties
                    ->setLocation($place->getName());
            }

            /** @var GraphLocation $location */
            if ($place && $location = $place->getLocation()) {
                $address = sprintf(
                    '%s, %s %s, %s',
                    $location->getStreet(),
                    $location->getZip(),
                    $location->getCity(),
                    $location->getCountry()
                );

                $properties
                    ->setLocationAddress($address)
                    ->setLatitude($location->getLongitude())
                    ->setLongitude($location->getLongitude());
            }

            return $properties;
        }

        return null;
    }

    protected function createRideFromEvent(GraphEvent $event): Ride
    {
        $ride = new Ride();

        $ride
            ->setTitle($event->getName())
            ->setDescription($event->getDescription())
            ->setDateTime($event->getStartTime());

        /**
         * @var GraphPage $place
         * @var GraphLocation $location
         */
        $place = $event->getPlace();

        if ($place) {
            $location = $place->getLocation();

            $ride
                ->setHasLocation(true)
                ->setLocation($place->getName());

            if ($location) {
                $ride
                    ->setLatitude($location->getLatitude())
                    ->setLongitude($location->getLongitude());
            }
        } else {
            $ride
                ->setHasLocation(false)
                ->setLocation(null);
        }

        if (!$event->getIsDateOnly()) {
            $ride->setHasTime(true);
        } else {
            $ride->setHasTime(false);
        }

        return $ride;
    }

    public function createRideForRide(Ride $ride): Ride
    {
        $event = $this->getEventForRide($ride);

        if ($event) {
            return $this->createRideFromEvent($event);
        }

        return null;
    }

    public function getEventForRide(Ride $ride): ?GraphEvent
    {
        if ($ride->getFacebook()) {
            $eventId = $this->getRideEventId($ride);

            $event = $this->facebookEventApi->queryEvent($eventId);

            return $event;
        }

        $graphEdge = $this->queryRideEventOnDay($ride);

        if (!$graphEdge) {
            return null;
        }

        /** @var GraphEvent $graphEvent */
        foreach ($graphEdge as $graphEvent) {
            if ($ride->getDateTime()->format('Y-m-d') === $graphEvent->getStartTime()->format('Y-m-d')) {
                return $graphEvent;
            }
        }

        return null;
    }

    protected function queryRideEventOnDay(Ride $ride): ?GraphEdge
    {
        $pageId = $this->getCityPageIdByRide($ride);

        if (!$pageId) {
            return null;
        }

        $since = DateTimeUtil::getDayStartDateTime($ride->getDateTime());
        $until = DateTimeUtil::getDayEndDateTime($ride->getDateTime());

        return $this->facebookEventApi->queryEvents($pageId, $since, $until);
    }

    protected function queryRideEventOnMonth(Ride $ride): ?GraphEdge
    {
        $pageId = $this->getCityPageIdByRide($ride);

        if (!$pageId) {
            return null;
        }

        $since = DateTimeUtil::getMonthStartDateTime($ride->getDateTime());
        $until = DateTimeUtil::getMonthEndDateTime($ride->getDateTime());

        return $this->facebookEventApi->queryEvents($pageId, $since, $until);
    }

    protected function getCityPageIdByRide(Ride $ride): ?string
    {
        return $this->getCityPageId($ride->getCity());
    }
}
