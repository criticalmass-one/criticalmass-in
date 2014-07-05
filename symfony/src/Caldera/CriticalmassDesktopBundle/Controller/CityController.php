<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CityController extends Controller
{
    public function listAction()
    {
        $cities = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->findBy(array(), array('city' => 'ASC'));

        return $this->render('CalderaCriticalmassDesktopBundle:City:list.html.twig', array('cities' => $cities));
    }
}
