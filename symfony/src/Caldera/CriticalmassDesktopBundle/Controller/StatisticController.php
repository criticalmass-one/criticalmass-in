<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StatisticController extends Controller
{
    public function participantsAction()
    {
        $cities = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->findBy(array(), array('city' => 'ASC'));

        $rides = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findLatestRidesOrderByParticipants(new \DateTime('2014-06-01 00:00:00'), new \DateTime('2014-06-30 23:59:59'));

        $maxParticipants = 0;
        $totalParticipants = 0;

        foreach ($rides as $ride)
        {
            if ($ride->getEstimatedParticipants() > $maxParticipants)
            {
                $maxParticipants = $ride->getEstimatedParticipants();
            }

            $totalParticipants += $ride->getEstimatedParticipants();
        }

        return $this->render('CalderaCriticalmassDesktopBundle:Statistic:participants.html.twig', array('cities' => $cities, 'rides' => $rides, 'maxParticipants' => $maxParticipants, 'totalParticipants' => $totalParticipants));
    }
}
