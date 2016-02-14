<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Router;

use Caldera\Bundle\CriticalmassCoreBundle\Board\Board\CityBoard;
use Caldera\Bundle\CriticalmassCoreBundle\Board\Board\RideBoard;
use Caldera\Bundle\CriticalmassCoreBundle\Board\Thread\RideThread;
use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Content;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Photo;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Region;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Thread;
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

        if ($object instanceof CityBoard) {
            return $this->generateCityBoardUrl($object, $referenceType);
        }

        if ($object instanceof Thread) {
            return $this->generateThreadUrl($object, $referenceType);
        }

        if ($object instanceof RideBoard) {
            return $this->generateRideBoardUrl($object, $referenceType);
        }

        if ($object instanceof RideThread) {
            return $this->generateCityRideThreadUrl($object, $referenceType);
        }

        if ($object instanceof Region) {
            return $this->generateRegionUrl($object, $referenceType);
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

    private function generateCityBoardUrl(CityBoard $cityTalkBoard, $referenceType)
    {
        $route = 'caldera_criticalmass_board_cityboard';

        $parameters = [
            'citySlug' => $cityTalkBoard->getCity()->getMainSlugString()
        ];

        return parent::generate($route, $parameters, $referenceType);
    }

    private function generateRideBoardUrl(RideBoard $cityRideBoard, $referenceType)
    {
        $route = 'caldera_criticalmass_board_rideboard';

        $parameters = [
            'citySlug' => $cityRideBoard->getCity()->getMainSlugString()
        ];

        return parent::generate($route, $parameters, $referenceType);
    }

    private function generateCityRideThreadUrl(RideThread $cityRideThread, $referenceType)
    {
        $route = 'caldera_criticalmass_board_ridethread';

        $parameters = [
            'citySlug' => $cityRideThread->getRide()->getCity()->getMainSlugString(),
            'rideDate' => $cityRideThread->getRide()->getFormattedDate()
        ];

        return parent::generate($route, $parameters, $referenceType);
    }

    private function generateThreadUrl(Thread $thread, $referenceType)
    {
        $route = 'caldera_criticalmass_board_citythread';

        $parameters = [
            'citySlug' => $thread->getCity()->getMainSlugString(),
            'threadId' => $thread->getId()
        ];

        return parent::generate($route, $parameters, $referenceType);
    }

    private function generateRegionUrl(Region $region, $referenceType)
    {
        if ($region->getParent() == null) {
            return parent::generate(
                'caldera_criticalmass_region_world', [], $referenceType);
        } elseif ($region->getParent()->getParent() == null) {
            return parent::generate(
                'caldera_criticalmass_region_world_region_1',
                [
                    'slug1' => $region->getSlug()
                ],
                $referenceType);
        } elseif ($region->getParent()->getParent()->getParent() == null) {
            return parent::generate(
                'caldera_criticalmass_region_world_region_2',
                [
                    'slug1' => $region->getSlug(),
                    'slug2' => $region->getParent()->getSlug()
                ],
                $referenceType);
        } elseif ($region->getParent()->getParent()->getParent() == null) {
            return parent::generate(
                'caldera_criticalmass_region_world_region_3',
                [
                    'slug1' => $region->getSlug(),
                    'slug2' => $region->getParent()->getSlug(),
                    'slug3' => $region->getParent()->getParent()->getSlug()
                ],
                $referenceType);
        }
    }
}
