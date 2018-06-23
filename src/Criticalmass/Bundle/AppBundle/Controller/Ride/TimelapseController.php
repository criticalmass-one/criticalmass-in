<?php

namespace Criticalmass\Bundle\AppBundle\Controller\Ride;

use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\Track;
use Criticalmass\Bundle\AppBundle\Criticalmass\Gps\LatLngListGenerator\TimeLatLngListGenerator;
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
