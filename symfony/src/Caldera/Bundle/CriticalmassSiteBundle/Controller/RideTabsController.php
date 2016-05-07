<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Facebook\FacebookEventRideApi;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\RideEstimateType;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\RideType;
use Caldera\Bundle\CriticalmassCoreBundle\Statistic\RideEstimate\RideEstimateService;
use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\RideEstimate;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Weather;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class RideTabsController extends AbstractController
{
    public function renderPhotosTabAction(Request $request, Ride $ride)
    {
        $photos = $this->getPhotoRepository()->findPhotosByRide($ride);

        return $this->render(
            'CalderaCriticalmassSiteBundle:RideTabs:GalleryTab.html.twig',
            [
                'ride' => $ride,
                'photos' => $photos,
                'dateTime' => new \DateTime()
            ]
        );
    }

    public function renderTracksTabAction(Request $request, Ride $ride)
    {
        $tracks = $this->getTrackRepository()->findSuitableTracksByRide($ride);

        return $this->render(
            'CalderaCriticalmassSiteBundle:RideTabs:TracksTab.html.twig',
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
            'CalderaCriticalmassSiteBundle:RideTabs:PostsTab.html.twig',
            [
                'ride' => $ride,
            ]
        );
    }

    public function renderSubridesTabAction(Request $request, Ride $ride)
    {
        $subrides = $this->getSubrideRepository()->getSubridesForRide($ride);

        return $this->render(
            'CalderaCriticalmassSiteBundle:RideTabs:SubridesTab.html.twig',
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
            'CalderaCriticalmassSiteBundle:RideTabs:StatisticTab.html.twig',
            [
                'ride' => $ride,
                'dateTime' => new \DateTime()
            ]
        );
    }

    public function renderMusicTabAction(Request $request, Ride $ride)
    {
        return $this->render(
            'CalderaCriticalmassSiteBundle:RideTabs:MusicTab.html.twig',
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
            $weatherForecast = $weather->getTemperatureEvening() . ' °C, ' . $weather->getWeatherDescription();
        } else {
            $weatherForecast = null;
        }

        $estimateForm = $this->createForm(
            new RideEstimateType(),
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
            'CalderaCriticalmassSiteBundle:RideTabs:DetailsTab.html.twig',
            [
                'ride' => $ride,
                'dateTime' => new \DateTime(),
                'estimateForm' => $estimateForm->createView(),
                'weatherForecast' => $weatherForecast,
                'location' => $location,
                'incidentCounter' => $this->getIncidentRepository()->countByRide($ride)
            ]
        );
    }
}
