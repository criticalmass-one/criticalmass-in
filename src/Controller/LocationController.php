<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Entity\Location;
use App\Entity\Ride;
use App\Repository\LocationRepository;
use App\Repository\RideRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LocationController extends AbstractController
{
    public function listlocationsAction(
        LocationRepository $locationRepository,
        City $city
    ): Response {
        $locations = $locationRepository->findLocationsByCity($city);

        return $this->render('Location/list.html.twig', [
            'locations' => $locations,
        ]);
    }

    public function showAction(
        LocationRepository $locationRepository,
        RideRepository $rideRepository,
        Location $location
    ): Response {
        $rides = $rideRepository->findRidesForLocation($location);

        $locations = $locationRepository->findLocationsByCity($location->getCity());

        return $this->render('Location/show.html.twig', [
            'location' => $location,
            'locations' => $locations,
            'rides' => $rides,
            'ride' => null,
        ]);
    }

    public function rideAction(
        LocationRepository $locationRepository,
        RideRepository $rideRepository,
        Ride $ride
    ): Response {
        $location = $locationRepository->findLocationForRide($ride);

        if (!$location) {
            throw new NotFoundHttpException();
        }

        $rides = $rideRepository->findRidesForLocation($location);

        $locations = $locationRepository->findLocationsByCity($ride->getCity());

        return $this->render('Location/show.html.twig', [
            'location' => $location,
            'locations' => $locations,
            'rides' => $rides,
            'ride' => $ride,
        ]);
    }
}
