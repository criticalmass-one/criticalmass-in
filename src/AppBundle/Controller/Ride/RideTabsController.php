<?php

namespace AppBundle\Controller\Ride;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEstimate;
use AppBundle\Entity\Weather;
use AppBundle\Form\Type\RideEstimateType;
use Symfony\Component\HttpFoundation\Response;

class RideTabsController extends AbstractController
{
    public function renderPhotosTabAction(Ride $ride): Response
    {
        $photos = $this->getPhotoRepository()->findPhotosByRide($ride);

        return $this->render('AppBundle:RideTabs:GalleryTab.html.twig', [
            'ride' => $ride,
            'photos' => $photos,
            'dateTime' => new \DateTime(),
        ]);
    }

    public function renderTracksTabAction(Ride $ride): Response
    {
        $tracks = $this->getTrackRepository()->findTracksByRide($ride);

        return $this->render('AppBundle:RideTabs:TracksTab.html.twig', [
            'ride' => $ride,
            'tracks' => $tracks,
            'dateTime' => new \DateTime()
        ]);
    }

    public function renderPostsTabAction(Ride $ride): Response
    {
        return $this->render('AppBundle:RideTabs:PostsTab.html.twig', [
            'ride' => $ride,
        ]);
    }

    public function renderSubridesTabAction(Ride $ride): Response
    {
        $subrides = $this->getSubrideRepository()->getSubridesForRide($ride);

        return $this->render('AppBundle:RideTabs:SubridesTab.html.twig', [
            'ride' => $ride,
            'subrides' => $subrides,
            'dateTime' => new \DateTime(),
        ]);
    }

    public function renderStatisticTabAction(Ride $ride): Response
    {
        return $this->render('AppBundle:RideTabs:StatisticTab.html.twig', [
            'ride' => $ride,
            'dateTime' => new \DateTime(),
        ]);
    }

    public function renderMusicTabAction(Ride $ride): Response
    {
        return $this->render('AppBundle:RideTabs:MusicTab.html.twig', [
            'ride' => $ride,
            'dateTime' => new \DateTime(),
        ]);
    }

    public function renderDetailsTabAction(Ride $ride): Response
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

        $estimateForm = $this->createForm(RideEstimateType::class, new RideEstimate(), [
            'action' => $this->generateUrl('caldera_criticalmass_ride_addestimate', [
                'citySlug' => $ride->getCity()->getMainSlugString(),
                'rideDate' => $ride->getFormattedDate(),
            ])
        ]);

        $location = $this->getLocationRepository()->findLocationForRide($ride);

        return $this->render('AppBundle:RideTabs:DetailsTab.html.twig', [
            'ride' => $ride,
            'dateTime' => new \DateTime(),
            'estimateForm' => $estimateForm->createView(),
            'weatherForecast' => $weatherForecast,
            'location' => $location,
        ]);
    }
}
