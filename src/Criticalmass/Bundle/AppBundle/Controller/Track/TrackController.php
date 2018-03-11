<?php

namespace Criticalmass\Bundle\AppBundle\Controller\Track;

use Criticalmass\Component\UploadValidator\TrackValidator;
use Criticalmass\Component\UploadValidator\UploadValidatorException\TrackValidatorException\TrackValidatorException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\Track;
use Criticalmass\Bundle\AppBundle\Traits\TrackHandlingTrait;
use Criticalmass\Component\Gps\TrackTimeShift\TrackTimeShift;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Form\Type\VichFileType;

class TrackController extends AbstractController
{
    use TrackHandlingTrait;

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction()
    {
        /**
         * @var array Track
         */
        $tracks = $this->getTrackRepository()->findBy(
            [
                'user' => $this->getUser()->getId(),
                'deleted' => false
            ],
            [
                'startDateTime' => 'DESC'
            ]
        );

        return $this->render('AppBundle:Track:list.html.twig',
            array(
                'tracks' => $tracks
            )
        );
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function uploadAction(Request $request, Ride $ride, $embed = false): Response
    {
        $track = new Track();

        $form = $this->createFormBuilder($track)
            ->setAction($this->generateUrl('caldera_criticalmass_track_upload', [
                'citySlug' => $ride->getCity()->getMainSlugString(),
                'rideDate' => $ride->getFormattedDate(),
            ]))
            ->add('trackFile', VichFileType::class)
            ->getForm();

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->uploadPostAction($request, $track, $ride, $form, $embed);
        } else {
            return $this->uploadGetAction($request, $ride, $form, $embed);
        }
    }

    protected function uploadGetAction(Request $request, Ride $ride, Form $form, $embed): Response
    {
        return $this->render('AppBundle:Track:upload.html.twig', [
            'form' => $form->createView(),
            'ride' => $ride,
            'errorMessage' => null,
        ]);
    }

    public function uploadPostAction(Request $request, Track $track, Ride $ride, Form $form, $embed): Response
    {
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /**
             * @var Track $track
             */
            $track = $form->getData();

            /* Save the track so the Uploader will place the file at the file system */
            $em->persist($track);

            /**
             * @var TrackValidator $trackValidator
             */
            $trackValidator = $this->get('caldera.criticalmass.uploadvalidator.track');
            $trackValidator->loadTrack($track);

            try {
                $trackValidator->validate();
            } catch (TrackValidatorException $e) {
                return $this->render('AppBundle:Track:upload.html.twig', [
                    'form' => $form->createView(),
                    'ride' => $ride,
                    'errorMessage' => $e->getMessage(),
                ]);
            }

            $this->loadTrackProperties($track);

            $track->setRide($ride);
            $track->setUser($this->getUser());
            $track->setUsername($this->getUser()->getUsername());

            $em->persist($track);
            $em->flush();

            $this->addRideEstimate($track, $ride);
            $this->generateSimpleLatLngList($track);
            $this->generatePolyline($track);

            return $this->redirect($this->generateUrl('caldera_criticalmass_track_view', [
                'trackId' => $track->getId()
            ]));
        }

        return $this->render('AppBundle:Track:upload.html.twig', [
            'form' => $form->createView(),
            'ride' => $ride,
            'errorMessage' => null,
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("track", class="AppBundle:Track", options={"id" = "trackId"})
     */
    public function viewAction(Track $track): Response
    {
        if ($track && $track->getUser()->equals($this->getUser())) {
            return $this->render('AppBundle:Track:view.html.twig', [
                'track' => $track,
                'nextTrack' => $this->getTrackRepository()->getNextTrack($track),
                'previousTrack' => $this->getTrackRepository()->getPreviousTrack($track),
            ]);
        }

        throw $this->createAccessDeniedException();
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("track", class="AppBundle:Track", options={"id" = "trackId"})
     */
    public function downloadAction(Track $track): Response
    {
        if ($track->getUser()->equals($this->getUser())) {
            $path = $this->getParameter('kernel.root_dir');
            $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
            $filename = $helper->asset($track, 'trackFile');

            $trackContent = file_get_contents($path . '/../web' . $filename);

            $response = new Response();

            $response->headers->add([
                'Content-disposition' => 'attachment; filename=track.gpx',
                'Content-type',
                'text/plain',
            ]);

            $response->setContent($trackContent);

            return $response;
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_track_track_list'));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("track", class="AppBundle:Track", options={"id" = "trackId"})
     */
    public function toggleAction(Track $track): Response
    {
        $ride = $track->getRide();

        if ($track->getUser()->equals($this->getUser())) {
            $em = $this->getDoctrine()->getManager();
            $track->setEnabled(!$track->getEnabled());
            $em->merge($track);
            $em->flush();

            $this->get('caldera.criticalmass.statistic.rideestimate.track')->calculateEstimates($ride);
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_track_list'));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("track", class="AppBundle:Track", options={"id" = "trackId"})
     */
    public function deleteAction(Request $request, Track $track): Response
    {
        $ride = $track->getRide();

        if ($track->getUser()->equals($this->getUser())) {
            $track->setDeleted(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($track);
            $em->flush();

            $this->get('caldera.criticalmass.statistic.rideestimate.track')->calculateEstimates($ride);
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_track_list'));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("track", class="AppBundle:Track", options={"id" = "trackId"})
     */
    public function rangeAction(Request $request, Track $track): Response
    {
        $form = $this->createFormBuilder($track)
            ->setAction($this->generateUrl('caldera_criticalmass_track_range', [
                'trackId' => $track->getId()
            ]))
            ->add('startPoint', HiddenType::class)
            ->add('endPoint', HiddenType::class)
            ->getForm();

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->rangePostAction($request, $track, $form);
        } else {
            return $this->rangeGetAction($request, $track, $form);
        }
    }

    protected function rangeGetAction(Request $request, Track $track, Form $form): Response
    {
        $llag = $this->container->get('caldera.criticalmass.gps.latlnglistgenerator.simple');
        $llag->loadTrack($track);
        $llag->execute();

        return $this->render('AppBundle:Track:range.html.twig',
            [
                'form' => $form->createView(),
                'track' => $track,
                'latLngList' => $llag->getList(),
                'gapWidth' => $this->getParameter('track.gap_width')
            ]
        );
    }

    protected function rangePostAction(Request $request, Track $track, Form $form): Response
    {
        $form->handleRequest($request);

        if ($form->isValid() && $track && $track->getUser()->equals($this->getUser())) {
            /**
             * @var Track $track
             */
            $track = $form->getData();

            $this->generatePolyline($track);
            $this->saveLatLngList($track);
            $this->updateTrackProperties($track);
            $this->calculateRideEstimates($track);
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_track_list'));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("track", class="AppBundle:Track", options={"id" = "trackId"})
     */
    public function timeAction(Request $request, Track $track): Response
    {
        $form = $this->createFormBuilder($track)
            ->setAction($this->generateUrl('caldera_criticalmass_track_time', [
                'trackId' => $track->getId()
            ]))
            ->add('startDate', DateType::class)
            ->add('startTime', TimeType::class)
            ->getForm();

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->timePostAction($request, $track, $form);
        } else {
            return $this->timeGetAction($request, $track, $form);
        }
    }

    protected function timeGetAction(Request $request, Track $track, Form $form): Response
    {
        return $this->render('AppBundle:Track:time.html.twig', [
            'form' => $form->createView(),
            'track' => $track,
        ]);
    }

    protected function timePostAction(Request $request, Track $track, Form $form): Response
    {
        // catch the old dateTime before it is overridden by the form submit
        $oldDateTime = $track->getStartDateTime();

        // now get the new values
        $form->handleRequest($request);

        if ($form->isValid() && $track && $track->getUser()->equals($this->getUser())) {
            /**
             * @var Track $newTrack
             */
            $newTrack = $form->getData();

            $interval = $newTrack->getStartDateTime()->diff($oldDateTime);

            /**
             * @var TrackTimeShift $tts
             */
            $tts = $this->get('caldera.criticalmass.gps.timeshift.track');

            $tts->loadTrack($newTrack)->shift($interval)->saveTrack();

            $this->updateTrackProperties($track);
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_track_list'));
    }

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
            'ride' => $ride,
        ]);
    }

    protected function drawPostAction(Request $request, Ride $ride): Response
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
     * @ParamConverter("track", class="AppBundle:Track", options={"id" = "trackId"})
     */
    public function editAction(Request $request, Track $track): Response
    {
        $ride = $track->getRide();

        if ($track->getUser() !== $track->getUser()) {
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
