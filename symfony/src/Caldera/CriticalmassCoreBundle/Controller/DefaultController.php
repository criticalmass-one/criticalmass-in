<?php

namespace Caldera\CriticalmassCoreBundle\Controller;

use Caldera\CriticalmassCoreBundle\Entity\Track;
use Caldera\CriticalmassCoreBundle\Utility\GpxWriter\GpxWriter;
use Caldera\CriticalmassCoreBundle\Utility\StandardRideGenerator\StandardRideGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function gpxexportAction($rideId, $ticketId)
    {
        $ticket = $this->getDoctrine()->getRepository('CalderaCriticalmassGlympseBundle:Ticket')->find($ticketId);
        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->find($rideId);

        $positionArray = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Position')->findBy(array('ride' => $rideId, 'ticket' => $ticketId), array('timestamp' => 'ASC'));

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

        return new Response();
    }

    public function standardridesAction($year, $month)
    {
        $cities = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->findBy(array(), array('city' => 'ASC'));

        echo '<ul>';

        foreach ($cities as $city)
        {
            echo '<li>';
            echo '<strong>'.$city->getTitle().'</strong>';

            if ($city->getIsStandardable())
            {
                $srg = new StandardRideGenerator($city, $year, $month);
                $ride = $srg->execute();

                echo '<br />Lege folgende Tour an:';
                echo '<ul>';

                if ($ride->getHasTime())
                {
                    echo '<li>Datum und Uhrzeit: '.$ride->getDateTime()->format('Y-m-d H:i').'</li>';
                }
                else
                {
                    echo '<li>Datum: '.$ride->getDateTime()->format('Y-m-d').', Uhrzeit ist bislang unbekannt</li>';
                }

                if ($ride->getHasLocation())
                {
                    echo '<li>Treffpunkt: '.$ride->getLocation().' ('.$ride->getLatitude().'/'.$ride->getLongitude().')</li>';
                }
                else
                {
                    echo '<li>Treffpunkt ist bislang unbekannt</li>';
                }

                echo '<li>sichtbar von '.$ride->getVisibleSince()->format('Y-m-d H:i').' bis '.$ride->getVisibleUntil()->format('Y-m-d H:i').'</li>';
                echo '</ul>';

                $em = $this->getDoctrine()->getManager();
                $em->persist($ride);
                $em->flush();
            }
            else
            {
                echo '<br />Lege keine Tourdaten für diese Stadt an.';
            }

            echo '</li>';
        }

        echo '</ul>';

        return new Response();
    }
}
