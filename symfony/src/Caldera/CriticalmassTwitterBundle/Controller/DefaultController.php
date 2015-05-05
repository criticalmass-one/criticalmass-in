<?php

namespace Caldera\CriticalmassTwitterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalderaCriticalmassTwitterBundle:Default:index.html.twig', array('name' => $name));
    }
}
