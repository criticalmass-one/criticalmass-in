<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Controller\AbstractController;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Ride;
use App\Entity\RideEstimate;
use App\Entity\Weather;
use App\Form\Type\RideDisableType;
use App\Form\Type\RideEstimateType;
use App\Repository\LocationRepository;
use App\Repository\SocialNetworkProfileRepository;
use App\Repository\SubrideRepository;
use App\Repository\WeatherRepository;
use Symfony\Component\HttpFoundation\Response;

class RideTabsController extends AbstractController
{
    public function renderPhotosTabAction(Ride $ride): Response
    {
        $photos = $this->getPhotoRepository()->findPhotosByRide($ride);

        return $this->render('RideTabs/GalleryTab.html.twig', [
            'ride' => $ride,
            'photos' => $photos,
            'dateTime' => new \DateTime(),
        ]);
    }

    public function renderTracksTabAction(Ride $ride): Response
    {
        $tracks = $this->getTrackRepository()->findTracksByRide($ride);

        return $this->render('RideTabs/TracksTab.html.twig', [
            'ride' => $ride,
            'tracks' => $tracks,
            'dateTime' => new \DateTime()
        ]);
    }

    public function renderPostsTabAction(Ride $ride): Response
    {
        return $this->render('RideTabs/PostsTab.html.twig', [
            'ride' => $ride,
        ]);
    }

    public function renderSubridesTabAction(
        Ride $ride,
        SubrideRepository $subrideRepository
    ): Response {
        $subrides = $subrideRepository->getSubridesForRide($ride);

        return $this->render('RideTabs/SubridesTab.html.twig', [
            'ride' => $ride,
            'subrides' => $subrides,
            'dateTime' => new \DateTime(),
        ]);
    }

    public function renderStatisticTabAction(Ride $ride): Response
    {
        return $this->render('RideTabs/StatisticTab.html.twig', [
            'ride' => $ride,
            'dateTime' => new \DateTime(),
        ]);
    }

    public function renderDetailsTabAction(
        LocationRepository $locationRepository,
        WeatherRepository $weatherRepository,
        SocialNetworkProfileRepository $socialNetworkProfileRepository,
        Ride $ride,
        ObjectRouterInterface $objectRouter
    ): Response {
        /**
         * @var Weather $weather
         */
        $weather = $weatherRepository->findCurrentWeatherForRide($ride);

        if ($weather) {
            $weatherForecast = round($weather->getTemperatureEvening()) . ' °C, ' . $weather->getWeatherDescription();
        } else {
            $weatherForecast = null;
        }

        $estimateForm = $this->createForm(RideEstimateType::class, new RideEstimate(), [
            'action' => $objectRouter->generate($ride, 'caldera_criticalmass_ride_addestimate'),
        ]);

        $location = $locationRepository->findLocationForRide($ride);

        $disableForm = $this
            ->createForm(RideDisableType::class, $ride, [
                'action' => $objectRouter->generate($ride, 'caldera_criticalmass_ride_disable'),
            ])
            ->createView();

        return $this->render('RideTabs/DetailsTab.html.twig', [
            'ride' => $ride,
            'dateTime' => new \DateTime(),
            'estimateForm' => $estimateForm->createView(),
            'weatherForecast' => $weatherForecast,
            'location' => $location,
            'socialNetworkProfiles' => $socialNetworkProfileRepository->findByRide($ride),
            'disableForm' => $disableForm
        ]);
    }
}
