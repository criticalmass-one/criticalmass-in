<?php declare(strict_types=1);

namespace App\Controller\City;

use App\Controller\AbstractController;
use App\Entity\City;
use App\Criticalmass\SeoPage\SeoPageInterface;
use App\Event\View\ViewEvent;
use App\Repository\BlockedCityRepository;
use App\Repository\CityRepository;
use App\Repository\LocationRepository;
use App\Repository\PhotoRepository;
use App\Repository\RideRepository;
use App\Repository\SocialNetworkProfileRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CityController extends AbstractController
{
    #[Route('/{citySlug}/missingstats', name: 'caldera_criticalmass_city_missingstats', priority: 100)]
    public function missingStatsAction(
        RideRepository $rideRepository,
        City $city
    ): Response {
        return $this->render('City/missing_stats.html.twig', [
            'city' => $city,
            'rides' => $rideRepository->findRidesWithoutStatisticsForCity($city),
        ]);
    }

    #[Route('/{citySlug}/list', name: 'caldera_criticalmass_city_listrides', priority: 170)]
    public function listRidesAction(
        RideRepository $rideRepository,
        City $city
    ): Response {
        return $this->render('City/ride_list.html.twig', [
            'city' => $city,
            'rides' => $rideRepository->findRidesForCity($city),
        ]);
    }

    #[Route('/{citySlug}/galleries', name: 'caldera_criticalmass_city_listgalleries', priority: 100)]
    public function listGalleriesAction(
        PhotoRepository $photoRepository,
        SeoPageInterface $seoPage,
        City $city
    ): Response {
        $seoPage->setDescription('Übersicht über Fotos von Critical-Mass-Touren aus ' . $city->getCity());

        $result = $photoRepository->findRidesWithPhotoCounter($city);

        return $this->render('City/gallery_list.html.twig', [
            'city' => $city,
            'result' => $result,
        ]);
    }

    #[Route(
        '/{citySlug}',
        name: 'caldera_criticalmass_city_show',
        options: ['expose' => true],
        priority: 100
    )]
    public function showAction(
        Request $request,
        RideRepository $rideRepository,
        CityRepository $cityRepository,
        LocationRepository $locationRepository,
        SocialNetworkProfileRepository $socialNetworkProfileRepository,
        BlockedCityRepository $blockedCityRepository,
        PhotoRepository $photoRepository,
        SeoPageInterface $seoPage,
        EventDispatcherInterface $eventDispatcher,
        City $city = null
    ): Response {
        if (!$city) {
            $citySlug = $request->get('citySlug');

            if (!$citySlug) {
                throw $this->createNotFoundException('City not found');
            }

            return $this->forward('App\\Controller\\City\\MissingCityController::missingAction', [
                'citySlug' => $citySlug,
            ]);
        }

        //$eventDispatcher->dispatch(new ViewEvent($city), ViewEvent::NAME);

        $blocked = $blockedCityRepository->findCurrentCityBlock($city);

        if ($blocked) {
            return $this->render('City/blocked.html.twig', [
                'city' => $city,
                'blocked' => $blocked
            ]);
        }

        $seoPage
            ->setDescription('Informationen, Tourendaten, Tracks und Fotos von der Critical Mass in ' . $city->getCity())
            ->setCanonicalForObject($city)
            ->setTitle($city->getTitle());

        if ($city->getImageName()) {
            $seoPage->setPreviewPhoto($city);
        }

        return $this->render('City/show.html.twig', [
            'city' => $city,
            'currentRide' => $rideRepository->findCurrentRideForCity($city),
            'nearCities' => $cityRepository->findNearCities($city),
            'locations' => $locationRepository->findLocationsByCity($city),
            'photos' => $photoRepository->findSomePhotos(8, null, $city),
            'rides' => $rideRepository->findRidesForCity($city, 'DESC', 6),
            'socialNetworkProfiles' => $socialNetworkProfileRepository->findByCity($city),
        ]);
    }

    #[Route('/{citySlug}/locations', name: 'caldera_criticalmass_city_locations', priority: 100)]
    public function getlocationsAction(
        RideRepository $rideRepository,
        City $city
    ): Response {
        return new Response(json_encode($rideRepository->getLocationsForCity($city)), Response::HTTP_OK, [
            'Content-Type' => 'text/json',
        ]);
    }
}
