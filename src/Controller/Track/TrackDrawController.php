<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Controller\AbstractController;
use App\Entity\Ride;
use App\Entity\Track;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TrackDrawController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route(
        '/{citySlug}/{rideIdentifier}/drawtrack',
        name: 'caldera_criticalmass_track_draw',
        priority: 270
    )]
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
        if (!$this->isCsrfTokenValid('track_draw', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

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

    #[IsGranted('edit', 'track')]
    #[Route(
        '/track/{id}/edit',
        name: 'caldera_criticalmass_track_edit',
        priority: 270
    )]
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
        if (!$this->isCsrfTokenValid('track_draw', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

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
