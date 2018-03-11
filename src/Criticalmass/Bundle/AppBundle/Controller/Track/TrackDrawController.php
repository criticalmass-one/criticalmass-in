<?php

namespace Criticalmass\Bundle\AppBundle\Controller\Track;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\Track;
use Criticalmass\Bundle\AppBundle\Traits\TrackHandlingTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackDrawController extends AbstractController
{
    use TrackHandlingTrait;

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function drawAction(Request $request, Ride $ride): Response
    {
        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->drawPostAction($request, $ride);
        } else {
            return $this->drawGetAction($request, $ride);
        }
    }

    protected function drawGetAction(Request $request, Ride $ride): Response
    {
        return $this->render('AppBundle:Track:draw.html.twig', [
            'ride' => $ride
        ]);
    }

    protected function drawPostAction(Request $request, Ride $ride): Response
    {
        $polyline = $request->request->get('polyline');
        $geojson = $request->request->get('geojson');

        $track = new Track();

        $track->setCreationDateTime(new \DateTime())
            ->setPolyline($polyline)
            ->setGeoJson($geojson)
            ->setRide($ride)
            ->setSource(Track::TRACK_SOURCE_DRAW)
            ->setUser($this->getUser())
            ->setUsername($this->getUser()->getUsername())
            ->setTrackFilename('foo');

        $em = $this->getDoctrine()->getManager();
        $em->persist($track);
        $em->flush();

        return $this->redirectToRoute('caldera_criticalmass_track_list');
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("track", class="AppBundle:Track", options={"id" = "trackId"})
     */
    public function editAction(Request $request, Track $track): Response
    {
        $ride = $track->getRide();

        if ($track->getUser() != $track->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->editPostAction($request, $ride, $track);
        } else {
            return $this->editGetAction($request, $ride, $track);
        }
    }

    protected function editGetAction(Request $request, Ride $ride, Track $track): Response
    {
        return $this->render('AppBundle:Track:draw.html.twig', [
            'ride' => $ride,
            'track' => $track
        ]);
    }

    protected function editPostAction(Request $request, Ride $ride, Track $track): Response
    {
        $polyline = $request->request->get('polyline');
        $geojson = $request->request->get('geojson');

        $track->setPolyline($polyline);
        $track->setGeoJson($geojson);

        $em = $this->getDoctrine()->getManager();
        $em->persist($track);
        $em->flush();

        return $this->redirectToRoute('caldera_criticalmass_track_list');
    }
}
