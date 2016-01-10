<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Router;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router as sfRouter;

class Router extends sfRouter
{

    public function generate($name, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        if ($name instanceof Ride) {
            return $this->generateRideUrl($name, $referenceType);
        }

        return parent::generate($name, $parameters, $referenceType);
    }

    private function generateRideUrl(Ride $ride, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $route = 'caldera_criticalmass_ride_show';

        $parameters = [
            'citySlug' => $ride->getCity()->getMainSlugString(),
            'rideDate' => $ride->getFormattedDate()
        ];

        return parent::generate($route, $parameters, $referenceType);
    }
}
