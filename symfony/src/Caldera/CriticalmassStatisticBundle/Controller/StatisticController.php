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

        $topAverageParticipantsCities = array();

        foreach ($cities as $city)
        {
            $topAverageParticipantsCities[$city->calculateAverageRideParticipants()] = $city;
        }

        krsort($topAverageParticipantsCities);

        $topAverageParticipantsCities = array_slice($topAverageParticipantsCities, 0, 10, true);

        return $this->render('CalderaCriticalmassStatisticBundle:Statistic:index.html.twig', array('cities' => $cities, 'topParticipantRides' => $topParticipantsRides, 'topDistanceRides' => $topDistanceRides, 'topAverageParticipantsCities' => $topAverageParticipantsCities));
    }

    public function cityparticipantsAction(Request $request, $citySlug)
    {
        $city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug)->getCity();

        return $this->render('CalderaCriticalmassStatisticBundle:Statistic:cityparticipants.html.twig', array('city' => $city));
    }
}
