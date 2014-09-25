<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CalderaCriticalmassDesktopBundle:Default:index.html.twig');
    }

    public function slugindexAction($slug)
    {
        $city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($slug)->getCity();

        return $this->render('CalderaCriticalmassDesktopBundle:Default:index.html.twig', array('citySlug' => $city->getMainSlugString()));
    }

    public function aboutusAction()
    {
        return $this->render('CalderaCriticalmassDesktopBundle:Default:aboutus.html.twig');
    }

    public function glympseAction()
    {
        return $this->render('CalderaCriticalmassDesktopBundle:Default:glympse.html.twig');
    }

    public function criticalmassAction()
    {
        return $this->render('CalderaCriticalmassDesktopBundle:Default:criticalmass.html.twig');
    }

    public function impressAction()
    {
        return $this->render('CalderaCriticalmassDesktopBundle:Default:impress.html.twig');
    }
}
