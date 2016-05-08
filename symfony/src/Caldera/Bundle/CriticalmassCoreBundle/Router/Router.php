<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Router;

use Caldera\Bundle\CriticalmassCoreBundle\Board\Board\CityBoard;
use Caldera\Bundle\CriticalmassCoreBundle\Board\Board\RideBoard;
use Caldera\Bundle\CriticalmassCoreBundle\Board\Thread\RideThread;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Board;
use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Content;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Event;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Location;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Photo;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Region;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Thread;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router as sfRouter;

class Router extends sfRouter
{

    public function generate($object, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        if ($object instanceof Ride) {
            return $this->generateRideUrl($object, $referenceType);
        }

        if ($object instanceof Event) {
            return $this->generateEventUrl($object, $referenceType);
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

        if ($object instanceof Board) {
            return $this->generateBoardUrl($object, $referenceType);
        }

        if ($object instanceof Thread) {
            return $this->generateThreadUrl($object, $referenceType);
        }

        if ($object instanceof Location) {
            return $this->generateLocationUrl($object, $referenceType);
        }

        if ($object instanceof Track) {
            return $this->generateTrackUrl($object, $referenceType);
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

    protected function generateEventUrl(Event $event, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $route = 'caldera_criticalmass_event_show';

        $parameters = [
            'citySlug' => $event->getCity()->getMainSlugString(),
            'eventSlug' => $event->getSlug()
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
        $route = '';

        $parameters = [
            'citySlug' => $photo->getCity()->getMainSlugString(),
            'photoId' => $photo->getId()
        ];

        if ($photo->getRide()) {
            $route = 'caldera_criticalmass_photo_show_ride';

            $parameters['rideDate'] = $photo->getRide()->getFormattedDate();
        } else {
            $route = 'caldera_criticalmass_photo_show_event';

            $parameters['eventSlug'] = $photo->getEvent()->getSlug();
        }

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

    protected function generateLocationUrl(Location $location, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $route = 'caldera_criticalmass_location_show';

        $parameters = [
            'citySlug' => $location->getCity()->getSlug(),
            'locationSlug' => $location->getSlug()
        ];

        return parent::generate($route, $parameters, $referenceType);
    }

    private function generateBoardUrl(Board $board, $referenceType)
    {
        $route = 'caldera_criticalmass_board_listthreads';

        $parameters = [
            'boardSlug' => $board->getSlug()
        ];

        return parent::generate($route, $parameters, $referenceType);
    }

    private function generateTrackUrl(Track $track, $referenceType)
    {
        $route = 'caldera_criticalmass_track_view';

        $parameters = [
            'trackId' => $track->getId()
        ];

        return parent::generate($route, $parameters, $referenceType);
    }

    private function generateThreadUrl(Thread $thread, $referenceType)
    {
        /* Let’s see if this is a city thread */
        if ($thread->getCity()) {
            $route = 'caldera_criticalmass_board_viewcitythread';

            $parameters = [
                'threadSlug' => $thread->getSlug(),
                'citySlug' => $thread->getCity()->getSlug()
            ];
        } else {
            $route = 'caldera_criticalmass_board_viewthread';

            $parameters = [
                'threadSlug' => $thread->getSlug(),
                'boardSlug' => $thread->getBoard()->getSlug()
            ];
        }

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
                    'slug1' => $region->getParent()->getSlug(),
                    'slug2' => $region->getSlug()
                ],
                $referenceType);
        } elseif ($region->getParent()->getParent()->getParent()->getParent() == null) {
            return parent::generate(
                'caldera_criticalmass_region_world_region_3',
                [
                    'slug1' => $region->getParent()->getParent()->getSlug(),
                    'slug2' => $region->getParent()->getSlug(),
                    'slug3' => $region->getSlug()
                ],
                $referenceType);
        }
    }
}
