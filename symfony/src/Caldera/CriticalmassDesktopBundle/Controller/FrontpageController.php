<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class FrontpageController extends Controller
{
    public function indexAction()
    {
        $articles = $this->getDoctrine()->getRepository('CalderaCriticalmassBlogBundle:Article')->findBy(array());
            
        return $this->render('CalderaCriticalmassDesktopBundle:Frontpage:index.html.twig', array('blogArticles' => $articles));
    }
}
