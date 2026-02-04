<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Entity\Ride;
use App\Criticalmass\SeoPage\SeoPageInterface;
use App\Event\View\ViewEvent;
use App\Repository\BlockedCityRepository;
use App\Repository\LocationRepository;
use App\Repository\ParticipationRepository;
use App\Repository\PhotoRepository;
use App\Repository\RideRepository;
use App\Repository\SubrideRepository;
use App\Repository\TrackRepository;
use App\Repository\WeatherRepository;
use App\Controller\AbstractController;
use App\Entity\Weather;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RideController extends AbstractController
{
    public function listAction(
        RideRepository $rideRepository
    ): Response {
        $ridesResult = $rideRepository->findRidesInInterval();

        $rides = [];

        /** @var Ride $ride */
        foreach ($ridesResult as $ride) {
            $rides[$ride->getDateTime()->format('Y-m-d')][] = $ride;
        }

        return $this->render('Ride/list.html.twig', [
            'rides' => $rides,
        ]);
    }

    #[Route(
        '/{citySlug}/{rideIdentifier}',
        name: 'caldera_criticalmass_ride_show',
        requirements: ['citySlug' => '(?!api$)[^/]+'],
        options: ['expose' => true],
        priority: -100
    )]
    public function showAction(
        BlockedCityRepository $blockedCityRepository,
        LocationRepository $locationRepository,
        ParticipationRepository $participationRepository,
        SubrideRepository $subrideRepository,
        WeatherRepository $weatherRepository,
        TrackRepository $trackRepository,
        PhotoRepository $photoRepository,
        SeoPageInterface $seoPage,
        EventDispatcherInterface $eventDispatcher,
        ?Ride $ride = null
    ): Response {
        if (!$ride) {
            $this->redirectToRoute('caldera_criticalmass_calendar');
        }

        $blocked = $blockedCityRepository->findCurrentCityBlock($ride->getCity());

        if ($blocked) {
            return $this->render('Ride/blocked.html.twig', [
                'ride' => $ride,
                'blocked' => $blocked
            ]);
        }

        //$eventDispatcher->dispatch(new ViewEvent($ride), ViewEvent::NAME);

        $seoPage
            ->setDescription('Informationen, Strecken und Fotos von der Critical Mass in ' . $ride->getCity()->getCity() . ' am ' . $ride->getDateTime()->format('d.m.Y'))
            ->setCanonicalForObject($ride);

        if ($ride->getImageName()) {
            $seoPage->setPreviewPhoto($ride);
        } elseif ($ride->getFeaturedPhoto()) {
            $seoPage->setPreviewPhoto($ride->getFeaturedPhoto());
        }

        if ($ride->getSocialDescription()) {
            $seoPage->setDescription($ride->getSocialDescription());
        } elseif ($ride->getDescription()) {
            $seoPage->setDescription($ride->getDescription());
        }

        /** @var Weather $weather */
        $weather = $weatherRepository->findCurrentWeatherForRide($ride);

        if ($weather) {
            $weatherForecast = round($weather->getTemperatureEvening()) . ' °C, ' . $weather->getWeatherDescription();
        } else {
            $weatherForecast = null;
        }

        if ($this->getUser()) {
            $participation = $participationRepository->findParticipationForUserAndRide(
                $this->getUser(),
                $ride
            );
        } else {
            $participation = null;
        }

        return $this->render('Ride/show.html.twig', [
            'city' => $ride->getCity(),
            'ride' => $ride,
            'tracks' => $trackRepository->findTracksByRide($ride),
            'photos' => $photoRepository->findPhotosByRide($ride),
            'subrides' => $subrideRepository->getSubridesForRide($ride),
            'dateTime' => new \DateTime(),
            'weatherForecast' => $weatherForecast,
            'participation' => $participation,
            'location' => $locationRepository->findLocationForRide($ride),
        ]);
    }
}
