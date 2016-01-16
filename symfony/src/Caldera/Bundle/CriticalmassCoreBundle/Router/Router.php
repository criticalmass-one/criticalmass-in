<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Router;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Photo;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router as sfRouter;

class Router extends sfRouter
{

    public function generate($object, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        if ($object instanceof Ride) {
            return $this->generateRideUrl($object, $referenceType);
        }

        if ($object instanceof City) {
            return $this->generateCityUrl($object, $referenceType);
        }

        if ($object instanceof Photo) {
            return $this->generatePhotoUrl($object, $referenceType);
        }

        return parent::generate($object, $parameters, $referenceType);
    }

    protected function generateRideUrl(Ride $ride, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $route = 'caldera_criticalmass_ride_show';

        $parameters = [
            'citySlug' => $ride->getCity()->getMainSlugString(),
            'rideDate' => $ride->getFormattedDate()
        ];

        return parent::generate($route, $parameters, $referenceType);
    }

    protected function generateCityUrl(City $city, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $route = 'caldera_criticalmass_desktop_city_show';

        $parameters = [
            'citySlug' => $city->getMainSlugString()
        ];

        return parent::generate($route, $parameters, $referenceType);
    }

    protected function generatePhotoUrl(Photo $photo, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $route = 'caldera_criticalmass_photo_show';

        $parameters = [
            'citySlug' => $photo->getCity()->getMainSlugString(),
            'rideDate' => $photo->getRide()->getFormattedDate(),
            'photoId' => $photo->getId()
        ];

        return parent::generate($route, $parameters, $referenceType);
    }
}
