<?php

namespace Caldera\CriticalmassStatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class StatisticController extends Controller
{
    public function indexAction(Request $request)
    {
        $cities = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->findCities();

        return $this->render('CalderaCriticalmassStatisticBundle:Statistic:index.html.twig');
    }
}
