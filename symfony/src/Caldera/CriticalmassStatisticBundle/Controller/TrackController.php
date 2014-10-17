<?php

namespace Caldera\CriticalmassStatisticBundle\Controller;

use Caldera\CriticalmassCoreBundle\Entity\Track;
use Caldera\CriticalmassCoreBundle\Utility\GpxWriter\GpxWriter;
use Caldera\CriticalmassStatisticBundle\Entity\RideEstimate;
use Caldera\CriticalmassStatisticBundle\Utility\RideGuesser\RideGuesser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackController extends Controller
{
    public function gpxgenerateAction($rideId)
    {
        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->find($rideId);

        $tickets = $this->getDoctrine()->getRepository('CalderaCriticalmassGlympseBundle:Ticket')->findBy(array('city' => $ride->getCity()));

        foreach ($tickets as $ticket) {
            if ($ticket->belongsToRide($ride)) {
                $positionArray = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Position')->findBy(array('ride' => $rideId, 'ticket' => $ticket->getId()), array('timestamp' => 'ASC'));

                $gpx = new GpxWriter();
                $gpx->setPositionArray($positionArray);
                $gpx->execute();

                $gpxContent = $gpx->getGpxContent();

                $track = new Track();
                $track->setRide($ride);
                $track->setTicket($ticket);
                $track->setUsername($ticket->getDisplayname());
                $track->setCreationDateTime(new \DateTime());
                $track->setGpx($gpxContent);
                $track->setActivated(1);
                $track->generateMD5Hash();

                $startDateTime = new \DateTime();
                $startDateTime->setTimestamp($positionArray[0]->getTimestamp());
                $track->setStartDateTime($startDateTime);

                $endDateTime = new \DateTime();
                $endDateTime->setTimestamp($positionArray[count($positionArray) - 1]->getTimestamp());
                $track->setEndDateTime($endDateTime);

                $track->setPoints(count($positionArray));
                $track->setDistance(0);

                $manager = $this->getDoctrine()->getManager();
                $manager->persist($track);
                $manager->flush();
            }
        }

        return new Response();
    }

    public function listAction()
    {
        $tracks = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Track')->findBy(array('user' => $this->getUser()->getId()), array('startDateTime' => 'DESC'));

        foreach ($tracks as $track) {
            $track->setStartDateTime($track->getStartDateTime()->add(new \DateInterval('PT2H')));
            $track->setEndDateTime($track->getEndDateTime()->add(new \DateInterval('PT2H')));
        }

        return $this->render('CalderaCriticalmassStatisticBundle:Track:list.html.twig', array('tracks' => $tracks));
    }

    public function uploadAction(Request $request)
    {
        $errorList = array();
        $track = new Track();
        $form = $this->createFormBuilder($track)
            ->setAction($this->generateUrl('caldera_criticalmass_statistic_track_upload'))
            ->add('file')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
/*
            if (!($track->getFile())) {
                return $this->render('CalderaCriticalmassStatisticBundle:Track:upload.html.twig', array('form' => $form->createView()));
            }*/

            if ($track->handleUpload())
            {
                /* User and user name are redundant, yes. This is a preparation to have a chance to change the user name later without losing the dependency to the user. */
                $track->setUser($this->getUser());
                $track->setUsername($this->getUser()->getUsername());

                /* Now, bring up the RideGuesser. We wanna know where the user was riding. */
                $rg = new RideGuesser($this);
                $rg->setGpx($track->getGpx());
                $rg->guess();

                if ($track->getPoints() < 100) {
                    array_push($errorList, "tooFewPoints");
                }

                if ($track->getPoints() != $track->getTimeStamps()) {
                    array_push($errorList, "tooFewTimeStamps");
                }

                /* Let’s see. The RideGuesser could not detect a ride. */
                if ($rg->isImpossible())
                {
                    array_push($errorList, "noTourFound");
                }
                /* Okay, it found a distinct ride, so let’s bring up the magic. */
                elseif (($rg->isDistinct()) && (sizeof($errorList) == 0))
                {
                    /* Save the concurrent ride. */
                    $rides = $rg->getRides();
                    $ride = array_pop($rides);
                    $track->setRide($ride);

                    /* Extract the ride distance and duration into a RideEstimate entity. */
                    $re = new RideEstimate();
                    $re->setRide($ride);
                    $re->setUser($this->getUser());
                    $re->setEstimatedDistance($track->getDistance());
                    $re->setEstimatedDuration($track->getDuration());
                    $track->setRideEstimate($re);

                    /* Save the shit… */
                    $em->persist($re);
                    $em->persist($track);
                    $em->flush();

                    /* … aaaand recalculate all estimates for this ride. */
                    $this->get('caldera.criticalmassstatistic.rideestimate')->calculateEstimates($ride);

                    /* Throw the user back to his track list. */
                    return $this->redirect($this->generateUrl('caldera_criticalmass_statistic_track_list'));
                }
                /* No, that didn’t work. We cannot detect a distinct ride, so the user has to set a ride hisself. */
                elseif (sizeof($errorList) == 0) {
                    $em->persist($track);
                    $em->flush();

                    return $this->redirect($this->generateUrl('caldera_criticalmass_statistic_track_setride', array('trackId' => $track->getId())));
                }
            } else {
                array_push($errorList, "noXML");
            }
        } else {
            //array_push($errorList, "tooBig");
        }

        if (sizeof($errorList) > 0) {
            return $this->redirect($this->generateUrl('caldera_criticalmass_statistic_track_upload_failed', array('errorList' => $errorList)));
        }

        return $this->render('CalderaCriticalmassStatisticBundle:Track:upload.html.twig', array('form' => $form->createView()));
    }

    public function setrideAction(Request $request, $trackId)
    {
        $track = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Track')->findOneById($trackId);

        $rg = new RideGuesser($this);
        $rg->setGpx($track->getGpx());
        $rg->guess();
        $rides = $rg->getRides();

        $choices = array();

        foreach ($rides as $ride) {
            $choices[] = $ride;
        }

        $form = $this->createFormBuilder($track)
            ->add('ride', 'entity', array
            (
                'class' => 'CalderaCriticalmassCoreBundle:Ride',
                'property' => 'cityTitle',
                'label' => 'Tour auswählen',
                'required' => true,
                'choices' => $choices
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $re = new RideEstimate();
            $re->setRide($ride);
            $re->setUser($this->getUser());
            $re->setEstimatedDistance($track->getDistance());
            $re->setEstimatedDuration($track->getDuration());

            $em->persist($re);
            $em->flush();
            $track->setRideEstimate($re);
            $em->persist($track);
            $em->flush();

            return $this->redirect($this->generateUrl('caldera_criticalmass_statistic_track_list'));
        }

        return $this->render('CalderaCriticalmassStatisticBundle:Track:setride.html.twig', array('form' => $form->createView()));
    }

    public function uploadfailedAction()
    {
        return $this->render('CalderaCriticalmassStatisticBundle:Track:uploadfailed.html.twig');
    }

    public function viewAction(Request $request, $trackId)
    {
        $track = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Track')->findOneById($trackId);

        if ($track->getUser()->equals($this->getUser()))
        {
            return $this->render('CalderaCriticalmassStatisticBundle:Track:view.html.twig', array('track' => $track));
        }

        throw new AccessDeniedException('');
    }

    public function downloadAction(Request $request, $trackId)
    {
        $em = $this->getDoctrine()->getManager();
        $track = $em->find('CalderaCriticalmassCoreBundle:Track', $trackId);

        if ($track->getUser()->equals($this->getUser()))
        {
            header('Content-disposition: attachment; filename=track.gpx');
            header('Content-type: text/plain');

            echo $track->getGpx();
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_statistic_track_list'));
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
        $em = $this->getDoctrine()->getManager();
        $track = $em->find('CalderaCriticalmassCoreBundle:Track', $trackId);

        if ($track->getUser()->equals($this->getUser()))
        {
            $track->setActivated(!$track->getActivated());
            $em->merge($track);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_statistic_track_list'));
    }

    public function deleteAction(Request $request, $trackId)
    {
        $em = $this->getDoctrine()->getManager();
        $track = $em->find('CalderaCriticalmassCoreBundle:Track',$trackId);

        if ($track->getUser()->equals($this->getUser()))
        {
            //$re = $em->find('CalderaCriticalmassStatisticBundle:RideEstimate', $track->getRideEstimate());
            $em->remove($track);
            //$em->remove($re);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_statistic_track_list'));
    }
}