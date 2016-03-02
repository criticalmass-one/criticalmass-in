<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Gps\DistanceCalculator\TrackDistanceCalculator;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxReader\GpxReader;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxReader\TrackReader;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngArrayGenerator\SimpleLatLngArrayGenerator;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngListGenerator\RangeLatLngListGenerator;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngListGenerator\SimpleLatLngListGenerator;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\TrackChecker\TrackChecker;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\TrackTimeShift\TrackTimeShift;
use Caldera\Bundle\CriticalmassCoreBundle\Statistic\RideEstimate\RideEstimateService;
use Caldera\Bundle\CriticalmassCoreBundle\Uploader\TrackUploader\TrackUploader;
use Caldera\Bundle\CriticalmassCoreBundle\UploadValidator\TrackValidator;
use Caldera\Bundle\CriticalmassCoreBundle\UploadValidator\UploadValidatorException\TrackValidatorException\TrackValidatorException;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackController extends AbstractController
{
    public function listAction()
    {
        /**
         * @var array Track
         */
        $tracks = $this->getTrackRepository()->findBy(
            [
                'user' => $this->getUser()->getId()
            ],
            [
                'startDateTime' => 'DESC'
            ]
        );

        return $this->render('CalderaCriticalmassSiteBundle:Track:list.html.twig', 
            array(
                'tracks' => $tracks
            )
        );
    }

    public function uploadAction(Request $request, $citySlug, $rideDate, $embed = false)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);
        $track = new Track();

        $form = $this->createFormBuilder($track)
            ->setAction($this->generateUrl('caldera_criticalmass_track_upload',
            [
                'citySlug' => $ride->getCity()->getMainSlugString(),
                'rideDate' => $ride->getFormattedDate()
            ]))
            ->add('trackFile', 'vich_file')
            ->getForm();
        
        if ('POST' == $request->getMethod()) {
            return $this->uploadPostAction($request, $track, $ride, $form, $embed);
        } else {
            return $this->uploadGetAction($request, $ride, $form, $embed);
        }
    }
    
    protected function uploadGetAction(Request $request, Ride $ride, Form $form, $embed)
    {
        return $this->render('CalderaCriticalmassSiteBundle:Track:upload.html.twig',
            [
                'form' => $form->createView(),
                'ride' => $ride,
                'errorMessage' => null
            ]);
    }
    
    public function uploadPostAction(Request $request, Track $track, Ride $ride, Form $form, $embed)
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
                return $this->render(
                    'CalderaCriticalmassSiteBundle:Track:upload.html.twig',
                    [
                        'form' => $form->createView(),
                        'ride' => $ride,
                        'errorMessage' => $e->getMessage()
                    ]
                );
            }
            
            $this->loadTrackProperties($track);
            
            $track->setRide($ride);
            $track->setUser($this->getUser());
            $track->setUsername($this->getUser()->getUsername());
            
            $em->persist($track);
            $em->flush();

            $this->addRideEstimate($track, $ride);

            $this->generateSimpleLatLngList($track);

            return $this->redirect($this->generateUrl('caldera_criticalmass_track_view', ['trackId' => $track->getId()]));
        }

        return $this->render(
            'CalderaCriticalmassSiteBundle:Track:upload.html.twig', 
            [
                'form' => $form->createView(),
                'ride' => $ride,
                'errorMessage' => null
            ]
        );
    }

    protected function loadTrackProperties(Track $track)
    {
        /**
         * @var TrackReader $gr
         */
        $gr = $this->get('caldera.criticalmass.gps.trackreader');
        $gr->loadTrack($track);

        $track->setPoints($gr->countPoints());

        $track->setStartPoint(0);
        $track->setEndPoint($gr->countPoints() - 1);

        $track->setStartDateTime($gr->getStartDateTime());
        $track->setEndDateTime($gr->getEndDateTime());

        /**
         * @var TrackDistanceCalculator $tdc
         */
        $tdc = $this->get('caldera.criticalmass.gps.distancecalculator.track');
        $tdc->loadTrack($track);

        $track->setDistance($tdc->calculate());

        $track->setMd5Hash($gr->getMd5Hash());
    }

    protected function addRideEstimate(Track $track, Ride $ride)
    {
        /**
         * @var RideEstimateService $estimateService
         */
        $estimateService = $this->get('caldera.criticalmass.statistic.rideestimate.track');
        $estimateService->addEstimate($track);
        $estimateService->calculateEstimates($ride);
    }

    protected function generateSimpleLatLngList(Track $track)
    {
        /**
         * @var SimpleLatLngListGenerator $generator
         */
        $generator = $this->get('caldera.criticalmass.gps.latlnglistgenerator.simple');
        $list = $generator
            ->loadTrack($track)
            ->execute()
            ->getList();

        $track->setLatLngList($list);

        $em = $this->getDoctrine()->getManager();
        $em->persist($track);
        $em->flush();
    }

    public function viewAction(Request $request, $trackId)
    {
        $track = $this->getTrackRepository()->findOneById($trackId);

        if ($track && $track->getUser()->equals($this->getUser())) {
            return $this->render(
                'CalderaCriticalmassSiteBundle:Track:view.html.twig',
                [
                    'track' => $track,
                    'nextTrack' => $this->getTrackRepository()->getNextTrack($track),
                    'previousTrack' => $this->getTrackRepository()->getPreviousTrack($track)
                ]
            );
        }

        throw new AccessDeniedException('');
    }

    public function downloadAction(Request $request, $trackId)
    {
        $track = $this->getTrackRepository()->find($trackId);

        if ($track && $track->getUser()->equals($this->getUser()))
        {
            header('Content-disposition: attachment; filename=track.gpx');
            header('Content-type: text/plain');

            $track->loadTrack();

            echo $track->getGpx();
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_track_track_list'));
    }

    /**
     * Activate or deactivate the user’s track. Deactivating a track will hide it from public ride overviews.
     *
     * @param Request $request
     * @param $trackId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @author swahlen
     */
    public function toggleAction(Request $request, $trackId)
    {
        $track = $this->getTrackRepository()->find($trackId);
        $ride = $track->getRide();

        if ($track && $track->getUser()->equals($this->getUser()))
        {
            $em = $this->getDoctrine()->getManager();
            $track->setActivated(!$track->getActivated());
            $em->merge($track);
            $em->flush();

            $this->get('caldera.criticalmass.statistic.rideestimate.track')->calculateEstimates($ride);
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_track_list'));
    }

    public function deleteAction(Request $request, $trackId)
    {
        $track = $this->getTrackRepository()->find($trackId);
        $ride = $track->getRide();
        
        if ($track && $track->getUser()->equals($this->getUser()))
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($track);
            $em->flush();

            $this->get('caldera.criticalmass.statistic.rideestimate.track')->calculateEstimates($ride);
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_track_list'));
    }

    public function rangeAction(Request $request, $trackId)
    {
        $track = $this->getTrackRepository()->findOneById($trackId);

        $form = $this->createFormBuilder($track)
            ->setAction($this->generateUrl('caldera_criticalmass_track_range',
                [
                    'trackId' => $track->getId()
                ]
            ))
            ->add('startPoint', 'hidden')
            ->add('endPoint', 'hidden')
            ->getForm();

        if ('POST' == $request->getMethod()) {
            return $this->rangePostAction($request, $track, $form);
        } else {
            return $this->rangeGetAction($request, $track, $form);
        }
    }

    protected function rangeGetAction(Request $request, Track $track, Form $form)
    {
        $llag = $this->container->get('caldera.criticalmass.gps.latlnglistgenerator.simple');
        $llag->loadTrack($track);
        $llag->execute();

        return $this->render('CalderaCriticalmassSiteBundle:Track:range.html.twig', 
            [
                'form' => $form->createView(),
                'track' => $track,
                'latLngList' => $llag->getList(),
                'gapWidth' => $this->getParameter('track.gap_width')
            ]
        );
    }

    protected function rangePostAction(Request $request, Track $track, Form $form)
    {
        $form->handleRequest($request);

        if ($form->isValid() && $track && $track->getUser()->equals($this->getUser()))
        {
            /**
             * @var Track $track
             */
            $track = $form->getData();

            $this->saveLatLngList($track);
            $this->updateTrackProperties($track);
            $this->calculateRideEstimates($track);
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_track_list'));
    }

    protected function saveLatLngList(Track $track)
    {
        /**
         * @var RangeLatLngListGenerator $llag
         */
        $llag = $this->container->get('caldera.criticalmass.gps.latlnglistgenerator.range');
        $llag->loadTrack($track);
        $llag->execute();
        $track->setLatLngList($llag->getList());

        $em = $this->getDoctrine()->getManager();
        $em->persist($track);
        $em->flush();
    }

    protected function updateTrackProperties(Track $track)
    {
        /**
         * @var TrackReader $gr
         */
        $tr = $this->get('caldera.criticalmass.gps.trackreader');
        $tr->loadTrack($track);

        $track->setStartDateTime($tr->getStartDateTime());
        $track->setEndDateTime($tr->getEndDateTime());
        $track->setDistance($tr->calculateDistance());

        $em = $this->getDoctrine()->getManager();
        $em->persist($track);
        $em->flush();
    }

    protected function calculateRideEstimates(Track $track)
    {
        /**
         * @var RideEstimateService $res
         */
        $res = $this->get('caldera.criticalmass.statistic.rideestimate.track');
        $res->flushEstimates($track->getRide());

        $res->refreshEstimate($track->getRideEstimate());
        $res->calculateEstimates($track->getRide());
    }

    public function timeAction(Request $request, $trackId)
    {
        $track = $this->getTrackRepository()->findOneById($trackId);

        $form = $this->createFormBuilder($track)
            ->setAction($this->generateUrl(
                'caldera_criticalmass_track_time',
                [
                    'trackId' => $track->getId()
                ]
            ))
            ->add('startDate', 'date')
            ->add('startTime', 'time')
            ->getForm();

        if ('POST' == $request->getMethod()) {
            return $this->timePostAction($request, $track, $form);
        } else {
            return $this->timeGetAction($request, $track, $form);
        }
    }

    protected function timeGetAction(Request $request, Track $track, Form $form)
    {
        return $this->render('CalderaCriticalmassSiteBundle:Track:time.html.twig',
            [
                'form' => $form->createView(),
                'track' => $track,
            ]
        );
    }

    protected function timePostAction(Request $request, Track $track, Form $form)
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
}