<?php

namespace Criticalmass\Bundle\AppBundle\Controller\Ride;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEstimate;
use AppBundle\Entity\Weather;
use AppBundle\Form\Type\RideEstimateType;
use Symfony\Component\HttpFoundation\Request;

class RideTabsController extends AbstractController
{
    public function renderPhotosTabAction(Request $request, Ride $ride)
    {
        $photos = $this->getPhotoRepository()->findPhotosByRide($ride);

        return $this->render(
            'AppBundle:RideTabs:GalleryTab.html.twig',
            [
                'ride' => $ride,
                'photos' => $photos,
                'dateTime' => new \DateTime()
            ]
        );
    }

    public function renderTracksTabAction(Request $request, Ride $ride)
    {
        $tracks = $this->getTrackRepository()->findTracksByRide($ride);

        return $this->render(
            'AppBundle:RideTabs:TracksTab.html.twig',
            [
                'ride' => $ride,
                'tracks' => $tracks,
                'dateTime' => new \DateTime()
            ]
        );
    }

    public function renderPostsTabAction(Request $request, Ride $ride)
    {
        return $this->render(
            'AppBundle:RideTabs:PostsTab.html.twig',
            [
                'ride' => $ride,
            ]
        );
    }

    public function renderSubridesTabAction(Request $request, Ride $ride)
    {
        $subrides = $this->getSubrideRepository()->getSubridesForRide($ride);

        return $this->render(
            'AppBundle:RideTabs:SubridesTab.html.twig',
            [
                'ride' => $ride,
                'subrides' => $subrides,
                'dateTime' => new \DateTime()
            ]
        );
    }

    public function renderStatisticTabAction(Request $request, Ride $ride)
    {
        return $this->render(
            'AppBundle:RideTabs:StatisticTab.html.twig',
            [
                'ride' => $ride,
                'dateTime' => new \DateTime()
            ]
        );
    }

    public function renderMusicTabAction(Request $request, Ride $ride)
    {
        return $this->render(
            'AppBundle:RideTabs:MusicTab.html.twig',
            [
                'ride' => $ride,
                'dateTime' => new \DateTime()
            ]
        );
    }

    public function renderDetailsTabAction(Request $request, Ride $ride)
    {
        /**
         * @var Weather $weather
         */
        $weather = $this->getWeatherRepository()->findCurrentWeatherForRide($ride);

        if ($weather) {
            $weatherForecast = round($weather->getTemperatureEvening()) . ' °C, ' . $weather->getWeatherDescription();
        } else {
            $weatherForecast = null;
        }

        $estimateForm = $this->createForm(
            RideEstimateType::class,
            new RideEstimate(),
            [
                'action' => $this->generateUrl(
                    'caldera_criticalmass_ride_addestimate',
                    [
                        'citySlug' => $ride->getCity()->getMainSlugString(),
                        'rideDate' => $ride->getFormattedDate()
                    ]
                )
            ]
        );

        $location = $this->getLocationRepository()->findLocationForRide($ride);

        return $this->render(
            'AppBundle:RideTabs:DetailsTab.html.twig',
            [
                'ride' => $ride,
                'dateTime' => new \DateTime(),
                'estimateForm' => $estimateForm->createView(),
                'weatherForecast' => $weatherForecast,
                'location' => $location,
            ]
        );
    }
}
