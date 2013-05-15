<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalderaCriticalmassBundle:Default:index.html.twig', array('name' => $name));
    }
}
