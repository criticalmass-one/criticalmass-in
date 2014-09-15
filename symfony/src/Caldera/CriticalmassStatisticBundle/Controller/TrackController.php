<?php

namespace Caldera\CriticalmassStatisticBundle\Controller;

use Caldera\CriticalmassCoreBundle\Entity\Track;
use Caldera\CriticalmassCoreBundle\Utility\GpxWriter\GpxWriter;
use Caldera\CriticalmassStatisticBundle\Utility\RideGuesser\RideGuesser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackController extends Controller
{
    public function gpxgenerateAction($rideId)
    {
        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->find($rideId);

        $tickets = $this->getDoctrine()->getRepository('CalderaCriticalmassGlympseBundle:Ticket')->findBy(array('city' => $ride->getCity()));

        foreach ($tickets as $ticket)
        {
            if ($ticket->belongsToRide($ride))
            {
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

        foreach ($tracks as $track)
        {
            $track->setStartDateTime($track->getStartDateTime()->add(new \DateInterval('PT2H')));
            $track->setEndDateTime($track->getEndDateTime()->add(new \DateInterval('PT2H')));
        }

        return $this->render('CalderaCriticalmassStatisticBundle:Track:list.html.twig', array('tracks' => $tracks));
    }

    public function uploadAction(Request $request)
    {
        $track = new Track();
        $form = $this->createFormBuilder($track)
            ->add('file')
            ->add('save', 'submit', array('label' => 'Create Post'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $track->handleUpload();
            $track->setUser($this->getUser());
            $track->setUsername($this->getUser()->getUsername());
            $track->setDistance(0);

            $rg = new RideGuesser($this);
            $rg->setGpx($track->getGpx());
            $rg->guess();

            if (!$rg->isDistinct())
            {
                $em->persist($track);
                $em->flush();

                return $this->redirect($this->generateUrl('caldera_criticalmass_statistic_track_setride', array('trackId' => $track->getId())));
            }
            else
            {
                $rides = $rg->getRides();
                $ride = array_pop($rides);
                $track->setRide($ride);

                $em->persist($track);
                $em->flush();

                return $this->redirect($this->generateUrl('caldera_criticalmass_statistic_track_list'));
            }
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

        foreach ($rides as $ride)
        {
            $choices[] = $ride;
        }

        $form = $this->createFormBuilder($track)
            ->add('ride', 'entity', array
            (
                'class' => 'CalderaCriticalmassCoreBundle:Ride',
                'property'=> 'cityTitle',
                'label' => 'Tour',
                'required' => true,
                'choices' => $choices
            ))
            ->add('save', 'submit', array('label' => 'Create Post'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($track);
            $em->flush();

            return $this->redirect($this->generateUrl('caldera_criticalmass_statistic_track_list'));
        }

        return $this->render('CalderaCriticalmassStatisticBundle:Track:setride.html.twig', array('form' => $form->createView()));
    }

    public function viewAction(Request $request, $trackId)
    {
        $track = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Track')->findOneById($trackId);

        return $this->render('CalderaCriticalmassStatisticBundle:Track:view.html.twig', array('track' => $track));
    }
}
