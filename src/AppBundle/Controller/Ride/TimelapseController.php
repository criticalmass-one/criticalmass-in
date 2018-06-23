<?php

namespace AppBundle\Controller\Ride;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Ride;
use AppBundle\Entity\Track;
use AppBundle\Criticalmass\Gps\LatLngListGenerator\TimeLatLngListGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class TimelapseController extends AbstractController
{
    /**
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function showAction(Ride $ride): Response
    {
        $tracks = $this->getTrackRepository()->findTracksByRide($ride);

        return $this->render('AppBundle:Timelapse:show.html.twig', [
            'ride' => $ride,
            'tracks' => $tracks,
        ]);
    }

    /**
     * @ParamConverter("track", class="AppBundle:Track", options={"id" = "trackId"})
     */
    public function loadtrackAction(TimeLatLngListGenerator $generator, Track $track): Response
    {
        $list = $generator
            ->loadTrack($track)
            ->execute()
            ->getList();

        return new Response($list, 200, ['Content-Type' => 'text/json']);
    }
}
