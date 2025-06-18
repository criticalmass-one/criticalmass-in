<?php declare(strict_types=1);

namespace App\Controller\Track;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Controller\AbstractController;
use App\Entity\Ride;
use App\Entity\Track;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackDrawController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_USER')")
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
        return $this->render('Track/draw.html.twig', [
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

        $em = $this->managerRegistry->getManager();
        $em->persist($track);
        $em->flush();

        return $this->redirectToRoute('caldera_criticalmass_track_list');
    }

    /**
     * @Security("is_granted('edit', track)")
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
        return $this->render('Track/draw.html.twig', [
            'ride' => $ride,
            'track' => $track,
        ]);
    }

    protected function editPostAction(Request $request, Ride $ride, Track $track): Response
    {
        $polyline = $request->request->get('polyline');
        $geojson = $request->request->get('geojson');

        $track->setPolyline($polyline);
        $track->setGeoJson($geojson);

        $em = $this->managerRegistry->getManager();
        $em->persist($track);
        $em->flush();

        return $this->redirectToRoute('caldera_criticalmass_track_list');
    }
}
