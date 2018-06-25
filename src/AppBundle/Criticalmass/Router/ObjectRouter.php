<?php

namespace AppBundle\Criticalmass\Router;

use AppBundle\Entity\Board;
use AppBundle\Entity\City;
use AppBundle\Entity\Location;
use AppBundle\Entity\Photo;
use AppBundle\Entity\Region;
use AppBundle\Entity\Ride;
use AppBundle\Entity\Thread;
use AppBundle\Entity\Track;
use AppBundle\EntityInterface\RouteableInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class ObjectRouter
{
    /** @var RouterInterface $router */
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function generate(
        RouteableInterface $object,
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        $classNameParts = explode('\\', get_class($object));
        $className = array_pop($classNameParts);

        $methodName = sprintf('generate%sUrl', $className);

        return $this->$methodName($object, $referenceType);
    }

    protected function generateRideUrl(Ride $ride, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        $route = 'caldera_criticalmass_ride_show';

        $parameters = [
            'citySlug' => $ride->getCity()->getMainSlugString(),
            'rideDate' => $ride->getFormattedDate()
        ];

        return $this->router->generate($route, $parameters, $referenceType);
    }

    protected function generateCityUrl(City $city, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        $route = 'caldera_criticalmass_city_show';

        $parameters = [
            'citySlug' => $city->getMainSlugString()
        ];

        return $this->router->generate($route, $parameters, $referenceType);
    }

    protected function generatePhotoUrl(Photo $photo, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        $route = 'caldera_criticalmass_photo_show_ride';

        $parameters = [
            'citySlug' => $photo->getCity()->getMainSlugString(),
            'photoId' => $photo->getId()
        ];

        $parameters['rideDate'] = $photo->getRide()->getFormattedDate();

        return $this->router->generate($route, $parameters, $referenceType);
    }

    protected function generateLocationUrl(
        Location $location,
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        $route = 'caldera_criticalmass_location_show';

        $parameters = [
            'citySlug' => $location->getCity()->getSlug(),
            'locationSlug' => $location->getSlug()
        ];

        return $this->router->generate($route, $parameters, $referenceType);
    }

    protected function generateBoardUrl(Board $board, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        $route = 'caldera_criticalmass_board_listthreads';

        $parameters = [
            'boardSlug' => $board->getSlug()
        ];

        return $this->router->generate($route, $parameters, $referenceType);
    }

    protected function generateTrackUrl(Track $track, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        $route = 'caldera_criticalmass_track_view';

        $parameters = [
            'trackId' => $track->getId()
        ];

        return $this->router->generate($route, $parameters, $referenceType);
    }

    protected function generateThreadUrl(
        Thread $thread,
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
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

        return $this->router->generate($route, $parameters, $referenceType);
    }

    protected function generateRegionUrl(
        Region $region,
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        if ($region->getParent() == null) {
            return $this->router->generate(
                'caldera_criticalmass_region_world', [], $referenceType);
        } elseif ($region->getParent()->getParent() == null) {
            return $this->router->generate(
                'caldera_criticalmass_region_world_region_1',
                [
                    'slug1' => $region->getSlug()
                ],
                $referenceType);
        } elseif ($region->getParent()->getParent()->getParent() == null) {
            return $this->router->generate(
                'caldera_criticalmass_region_world_region_2',
                [
                    'slug1' => $region->getParent()->getSlug(),
                    'slug2' => $region->getSlug()
                ],
                $referenceType);
        } elseif ($region->getParent()->getParent()->getParent()->getParent() == null) {
            return $this->router->generate(
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
