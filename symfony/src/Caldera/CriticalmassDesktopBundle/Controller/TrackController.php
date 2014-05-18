<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TrackController extends Controller
{
    public function showrideAction($rideId)
    {
        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findOneById($rideId);

        if (!$ride)
        {
            throw $this->createNotFoundException('There is no ride with the ID '.$rideId.'.');
        }

        return $this->render('CalderaCriticalmassDesktopBundle:Live:show.html.twig');
    }
}
