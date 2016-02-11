<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Router;

use Caldera\Bundle\CriticalmassCoreBundle\Board\Board\CityImageCommentBoard;
use Caldera\Bundle\CriticalmassCoreBundle\Board\Board\CityRideBoard;
use Caldera\Bundle\CriticalmassCoreBundle\Board\Board\CityTalkBoard;
use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Content;
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

        if ($object instanceof Content) {
            return $this->generateContentUrl($object, $referenceType);
        }

        if ($object instanceof CityTalkBoard) {
            return $this->generateCityTalkBoardUrl($object, $referenceType);
        }

        if ($object instanceof CityRideBoard) {
            return $this->generateCityRideBoardUrl($object, $referenceType);
        }

        if ($object instanceof CityImageCommentBoard) {
            return $this->generateCityImageCommentBoardUrl($object, $referenceType);
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

    protected function generateContentUrl(Content $content, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $route = 'caldera_criticalmass_content_display';

        $parameters = [
            'slug' => $content->getSlug()
        ];

        return parent::generate($route, $parameters, $referenceType);
    }

    private function generateCityTalkBoardUrl(CityTalkBoard $cityTalkBoard, $referenceType)
    {
        $route = 'caldera_criticalmass_board_talkboard';

        $parameters = [
            'citySlug' => $cityTalkBoard->getCity()->getMainSlugString()
        ];

        return parent::generate($route, $parameters, $referenceType);
    }

    private function generateCityRideBoardUrl(CityRideBoard $cityRideBoard, $referenceType)
    {
        $route = 'caldera_criticalmass_board_rideboard';

        $parameters = [
            'citySlug' => $cityRideBoard->getCity()->getMainSlugString()
        ];

        return parent::generate($route, $parameters, $referenceType);
    }

    private function generateCityImageCommentBoardUrl(CityImageCommentBoard $cityImageCommentBoard, $referenceType)
    {
        $route = 'caldera_criticalmass_board_imagecommentboard';

        $parameters = [
            'citySlug' => 'hamburg'
        ];

        return parent::generate($route, $parameters, $referenceType);
    }
}
