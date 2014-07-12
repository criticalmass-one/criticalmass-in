<?php

namespace Caldera\CriticalmassContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalderaCriticalmassContentBundle:Default:index.html.twig', array('name' => $name));
    }
}
