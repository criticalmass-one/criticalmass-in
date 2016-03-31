<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Facebook;

use Caldera\Bundle\CriticalmassModelBundle\Entity\FacebookRideProperties;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Facebook\GraphNodes\GraphEvent;
use Facebook\GraphNodes\GraphLocation;
use Facebook\GraphNodes\GraphPage;

class FacebookEventRideApi extends FacebookEventApi
{
    public function getEventPropertiesForRide(Ride $ride)
    {
        $eventId = $this->getRideEventId($ride);

        /**
         * @var GraphEvent $event
         */
        $event = $this->queryEvent($eventId, $this->standardFields);

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
                ->setNumberNoreply($event->getField('noreply_count'));

            /**
             * @var GraphPage $place
             */
            if ($place = $event->getPlace()) {
                $properties
                    ->setLocation($place->getName());
            }

            /**
             * @var GraphLocation $location
             */
            if ($place and $location = $place->getLocation()) {
                $properties
                    ->setLocationAddress($location->getStreet() . ', ' . $location->getZip() . ' ' . $location->getCity() . ', ' . $location->getCountry())
                    ->setLatitude($location->getLongitude())
                    ->setLongitude($location->getLongitude())
                ;
            }

            return $properties;
        }

        return null;
    }

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

        if ($event) {
            return $this->createRideFromEvent($event);
        }

        return null;
    }
}