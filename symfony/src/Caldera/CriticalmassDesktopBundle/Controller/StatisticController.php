<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StatisticController extends Controller
{
    public function indexAction()
    {
        $cities = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->findBy(array(), array('city' => 'ASC'));

        $ridesResult = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findLatestRidesOrderByParticipants(new \DateTime('2014-06-01 00:00:00'), new \DateTime('2014-06-30 23:59:59'));

        $rides = array();

        foreach ($cities as $city)
        {
            $rides[$city->getMainSlugString()] = null;
        }

        foreach ($ridesResult as $ride)
        {
            $rides[$ride->getCity()->getMainSlugString()] = $ride;
        }


        return $this->render('CalderaCriticalmassDesktopBundle:Statistic:index.html.twig', array('cities' => $cities, 'rides' => $rides));
    }
}
