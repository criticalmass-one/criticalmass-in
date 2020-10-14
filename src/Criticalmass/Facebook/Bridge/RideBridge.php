<?php declare(strict_types=1);

namespace App\Criticalmass\Facebook\Bridge;

use App\Entity\FacebookRideProperties;
use App\Entity\Ride;
use App\Entity\SocialNetworkProfile;
use App\Criticalmass\Facebook\Api\FacebookEventApi;
use App\Criticalmass\Util\DateTimeUtil;
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

            $ride->setLocation($place->getName());

            if ($location) {
                $ride
                    ->setLatitude($location->getLatitude())
                    ->setLongitude($location->getLongitude());
            }
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
        if ($this->hasFacebookEvent($ride)) {
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

    protected function hasFacebookEvent(Ride $ride): bool
    {
        /** @var SocialNetworkProfile $socialNetworkProfile */
        foreach ($ride->getSocialNetworkProfiles() as $socialNetworkProfile) {
            if ($socialNetworkProfile->getNetwork() === 'facebook_event') {
                return true;
            }
        }

        return false;
    }
}
