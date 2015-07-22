<?php

namespace Caldera\CriticalmassMobileBundle\Controller;

use Caldera\CriticalmassStatisticBundle\Utility\Trackable;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MultiPageController extends Controller
{
    public function mainAction()
    {
        return $this->render('CalderaCriticalmassMobileBundle:MultiPage:main.html.twig');
    }

    public function slugindexAction($slug)
    {
        $city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($slug)->getCity();

        return $this->render('CalderaCriticalmassMobileBundle:MultiPage:main.html.twig', array('citySlug' => $city->getMainSlugString()));
    }
}
