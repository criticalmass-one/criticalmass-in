<?php

namespace Caldera\CriticalmassStatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class StatisticController extends Controller
{
    public function indexAction(Request $request)
    {
        $cities = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->findCities();

        $topParticipantsRides = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findBy(array(), array('estimatedParticipants' => 'DESC'), 10);
        $topDistanceRides = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findBy(array(), array('estimatedDistance' => 'DESC'), 10);
        $topAverageParticipantsRides = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->findCitiesByAverageParticipants(10);

        return $this->render('CalderaCriticalmassStatisticBundle:Statistic:index.html.twig', array('cities' => $cities, 'topParticipantRides' => $topParticipantsRides, 'topDistanceRides' => $topDistanceRides, 'topAverageParticipantsRides' => $topAverageParticipantsRides));
    }

    public function cityparticipantsAction(Request $request, $citySlug)
    {
        $city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug)->getCity();

        return $this->render('CalderaCriticalmassStatisticBundle:Statistic:cityparticipants.html.twig', array('city' => $city));
    }
}
