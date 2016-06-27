<?php

namespace Caldera\Bundle\CriticalmassOneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalderaCriticalmassOneBundle:Default:index.html.twig', array('name' => $name));
    }
}
