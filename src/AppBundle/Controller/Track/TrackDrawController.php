<?php

namespace AppBundle\Controller\Track;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Ride;
use AppBundle\Entity\Track;
use AppBundle\Traits\TrackHandlingTrait;
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
     * @Security("is_granted('edit', track)")
     * @ParamConverter("track", class="AppBundle:Track", options={"id" = "trackId"})
     */
    public function editAction(Request $request, Track $track): Response
    {
        $ride = $track->getRide();

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
