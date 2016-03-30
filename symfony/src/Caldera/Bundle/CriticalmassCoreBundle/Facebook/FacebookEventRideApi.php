<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Facebook;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Facebook\GraphNodes\GraphEvent;
use Facebook\GraphNodes\GraphLocation;
use Facebook\GraphNodes\GraphPage;

class FacebookEventRideApi extends FacebookEventApi
{
    protected function createRideFromEvent(GraphEvent $event)
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

            $address = ($location->getStreet() ? $location->getStreet().', ' : null).$location->getZip().' '.$location->getCity();

            $ride
                ->setHasLocation(true)
                ->setLocation($address)
                ->setLatitude($location->getLatitude())
                ->setLongitude($location->getLongitude())
            ;
        } else {
            $ride
                ->setHasLocation(false)
                ->setLocation(null)
            ;
        }

        if (!$event->getIsDateOnly()) {
            $ride->setHasTime(true);
        } else {
            $ride->setHasTime(false);
        }

        return $ride;
    }

    public function createRideForRide(Ride $ride)
    {
        $event = $this->getEventForRide($ride);

        return $this->createRideFromEvent($event);
    }
}