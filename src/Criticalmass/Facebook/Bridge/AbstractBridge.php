<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Facebook\Bridge;

use AppBundle\Entity\City;
use AppBundle\Entity\Ride;

abstract class AbstractBridge
{
    protected function getRideEventId(Ride $ride): ?string
    {
        $facebook = $ride->getFacebook();

        if ($facebook) {
            $facebook = rtrim($facebook, '/');

            $parts = explode('/', $facebook);

            $eventId = array_pop($parts);

            return $eventId;
        }

        return null;
    }

    protected function getCityPageId(City $city): ?string
    {
        $facebook = $city->getFacebook();

        if ($facebook) {
            $facebook = rtrim($facebook, '/');

            $parts = explode('/', $facebook);

            $pageId = array_pop($parts);

            return $pageId;
        }

        return null;
    }
}
