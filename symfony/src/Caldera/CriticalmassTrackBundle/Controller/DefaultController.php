<?php

namespace Caldera\CriticalmassTrackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalderaCriticalmassTrackBundle:Default:index.html.twig', array('name' => $name));
    }
}
