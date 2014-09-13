<?php

namespace Caldera\CriticalmassStatisticBundle\Controller;

use Caldera\CriticalmassCoreBundle\Entity\Track;
use Caldera\CriticalmassCoreBundle\Utility\GpxWriter\GpxWriter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

                $manager = $this->getDoctrine()->getManager();
                $manager->persist($track);
                $manager->flush();
            }
        }

        return new Response();
    }

    public function listAction()
    {
        $tracks = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Track')->findBy(array('user' => $this->getUser()->getId()));

        return $this->render('CalderaCriticalmassStatisticBundle:Track:list.html.twig', array('tracks' => $tracks));
    }
}
