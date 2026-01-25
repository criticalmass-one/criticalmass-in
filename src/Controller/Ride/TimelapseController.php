<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Controller\AbstractController;
use App\Criticalmass\Geo\GpxService\GpxServiceInterface;
use App\Entity\Ride;
use App\Entity\Track;
use App\Repository\TrackRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TimelapseController extends AbstractController
{
    #[Route(
        '/{citySlug}/{rideIdentifier}/timelapse',
        name: 'caldera_criticalmass_timelapse_homepage',
        priority: 135
    )]
    public function showAction(
        TrackRepository $trackRepository,
        Ride $ride
    ): Response {
        $tracks = $trackRepository->findTracksByRide($ride);

        return $this->render('Timelapse/show.html.twig', [
            'ride' => $ride,
            'tracks' => $tracks,
        ]);
    }

    #[Route(
        '/{citySlug}/{rideIdentifier}/timelapse/load/{id}',
        name: 'caldera_criticalmass_timelapse_load',
        options: ['expose' => true],
        priority: 135
    )]
    public function loadtrackAction(GpxServiceInterface $gpxService, Track $track): Response
    {
        $list = $gpxService->generateTimeLatLngList($track);

        return new Response($list, Response::HTTP_OK, ['Content-Type' => 'text/json']);
    }
}
