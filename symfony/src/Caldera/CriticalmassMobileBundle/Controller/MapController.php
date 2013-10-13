<?php

namespace Caldera\CriticalmassMobileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MapController extends Controller
{
    public function showcityAction($citySlug)
    {
        $city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug)->getCity();

        return $this->render('CalderaCriticalmassMobileBundle:Map:showcity.html.twig', array('city' => $city));
    }
}
