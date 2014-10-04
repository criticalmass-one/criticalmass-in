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

    /**
     * Prepares a template with a pie chart containing the estimated participants of every tour in a specific month.
     *
     * @param Request $request
     * @param $year
     * @param $month
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rideparticipantsAction(Request $request, $year, $month)
    {
        if (!$year or !$month)
        {
            $dateTime = new \DateTime();
        }
        else
        {
            $dateTime = new \DateTime($year.'-'.$month.'-01');
        }

        /* Okay, now take the rides from the database and push them into our new array $rides with their estimated par-
        ticipants as their key so we can sort them afterwards. We’ll use a two-dimensional array here to cover cities
        with identical estimates. */
        $rides = array();
        $rideResult = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findRidesByDateTimeMonth($dateTime);

        foreach ($rideResult as $ride)
        {
            $rides[$ride->getEstimatedParticipants()][] = $ride;
        }

        /* Now sort this shit and keep the old keys as the index. */
        krsort($rides);

        /* We’ll use this interval to calculate the previous and the next month for the navigation. */
        $monthInterval = new \DateInterval('P1M');

        $previousMonth = clone $dateTime;
        $previousMonth->sub($monthInterval);

        $nextMonth = clone $dateTime;
        $nextMonth = $nextMonth->add($monthInterval);

        /* If the next month would be in the future, skip this case. */
        if ($nextMonth > new \DateTime())
        {
            $nextMonth = null;
        }

        return $this->render('CalderaCriticalmassStatisticBundle:Statistic:rideparticipants.html.twig', array('rides' => $rides, 'currentMonth' => $dateTime, 'previousMonth' => $previousMonth, 'nextMonth' => $nextMonth));
    }
}
