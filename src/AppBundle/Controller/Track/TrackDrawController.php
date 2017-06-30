<?php

namespace AppBundle\Controller\Track;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Ride;
use AppBundle\Entity\Track;
use AppBundle\Traits\TrackHandlingTrait;
use AppBundle\Gps\TrackTimeShift\TrackTimeShift;
use AppBundle\UploadValidator\TrackValidator;
use AppBundle\UploadValidator\UploadValidatorException\TrackValidatorException\TrackValidatorException;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Form\Type\VichFileType;

class TrackDrawController extends AbstractController
{
    use TrackHandlingTrait;

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function drawAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        if ('POST' == $request->getMethod()) {
            return $this->drawPostAction($request, $ride);
        } else {
            return $this->drawGetAction($request, $ride);
        }
    }

    protected function drawGetAction(Request $request, Ride $ride)
    {
        return $this->render(
            'AppBundle:Track:draw.html.twig',
            [
                'ride' => $ride
            ]
        );
    }

    protected function drawPostAction(Request $request, Ride $ride)
    {
        $polyline = $request->request->get('polyline');
        $geojson = $request->request->get('geojson');

        $track = new Track();

        $track->setCreationDateTime(new \DateTime());
        $track->setPolyline($polyline);
        $track->setGeoJson($geojson);
        $track->setRide($ride);
        $track->setSource(Track::TRACK_SOURCE_DRAW);
        $track->setUser($this->getUser());
        $track->setUsername($this->getUser()->getUsername());
        $track->setTrackFilename('foo');

        $em = $this->getDoctrine()->getManager();
        $em->persist($track);
        $em->flush();

        return $this->redirectToRoute('caldera_criticalmass_track_list');
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function editAction(Request $request, int $trackId)
    {
        /** @var Track $track */
        $track = $this->getTrackRepository()->find($trackId);
        $ride = $track->getRide();

        if ($track->getUser() != $track->getUser()) {
            return $this->createAccessDeniedException();
        }

        if ('POST' == $request->getMethod()) {
            return $this->editPostAction($request, $ride, $track);
        } else {
            return $this->editGetAction($request, $ride, $track);
        }
    }

    protected function editGetAction(Request $request, Ride $ride, Track $track)
    {
        return $this->render(
            'AppBundle:Track:draw.html.twig',
            [
                'ride' => $ride,
                'track' => $track
            ]
        );
    }

    protected function editPostAction(Request $request, Ride $ride, Track $track)
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
