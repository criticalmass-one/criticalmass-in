<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Controller\AbstractController;
use App\Criticalmass\Geo\LatLngListGenerator\TimeLatLngListGenerator;
use App\Entity\Ride;
use App\Entity\Track;
use App\Repository\TrackRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class TimelapseController extends AbstractController
{
    /**
     * @ParamConverter("ride", class="App:Ride")
     */
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

    /**
     * @ParamConverter("track", class="App:Track", options={"id" = "trackId"})
     */
    public function loadtrackAction(TimeLatLngListGenerator $generator, Track $track): Response
    {
        $list = $generator
            ->loadTrack($track)
            ->execute()
            ->getList();

        return new Response($list, Response::HTTP_OK, ['Content-Type' => 'text/json']);
    }
}
