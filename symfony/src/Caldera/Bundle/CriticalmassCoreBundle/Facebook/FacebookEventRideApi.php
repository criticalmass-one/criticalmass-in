<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Facebook;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Facebook\GraphNodes\GraphEvent;

class FacebookEventRideApi extends FacebookEventApi
{
    protected function createRideFromEvent(GraphEvent $event)
    {
        $ride = new Ride();

        $ride->setTitle($event->getName());
        $ride->setDescription($event->getDescription());

        $place = $event->getPlace();

        if ($place) {
            $ride->setHasLocation(true);
            $ride->setLocation($place);
        } else {
            $ride->setHasLocation(false);
            $ride->setLocation(null);
        }

        if (!$event->getIsDateOnly()) {
            $ride->setHasTime(true);
        } else {
            $ride->setHasTime(false);
        }

        $ride->setDateTime($event->getStartTime());

        return $ride;
    }

    public function createRideForRide(Ride $ride)
    {
        $event = $this->getEventForRide($ride);

        return $this->createRideFromEvent($event);
    }
}