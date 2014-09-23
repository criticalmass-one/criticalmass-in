<?php

namespace Caldera\CriticalmassTimelineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalderaCriticalmassTimelineBundle:Default:index.html.twig', array('name' => $name));
    }
}
