<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class FrontpageController extends Controller
{
    public function indexAction()
    {
        $dateTime = new \DateTime();
        
        $blogArticles = $this->getDoctrine()->getRepository('CalderaCriticalmassBlogBundle:Article')->findBy(array());
        $carouselRides = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findRecentRides(null, null, 5, 1000);
        
        return $this->render('CalderaCriticalmassDesktopBundle:Frontpage:index.html.twig',
            [
                'blogArticles' => $blogArticles,
                'carouselRides' => $carouselRides
            ]);
    }
}
