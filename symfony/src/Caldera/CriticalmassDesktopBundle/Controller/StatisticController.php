<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StatisticController extends Controller
{
    public function indexAction()
    {
        $cities = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->findBy(array(), array('city' => 'ASC'));

        $rides = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findLatestRidesOrderByParticipants(new \DateTime('2014-06-01 00:00:00'), new \DateTime('2014-06-30 23:59:59'));

        return $this->render('CalderaCriticalmassDesktopBundle:Statistic:index.html.twig', array('cities' => $cities, 'rides' => $rides));
    }
}
