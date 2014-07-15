<?php

namespace Caldera\CriticalmassGlympseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalderaCriticalmassGlympseBundle:Default:index.html.twig', array('name' => $name));
    }
}
