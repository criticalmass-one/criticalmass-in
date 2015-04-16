<?php

namespace Caldera\CriticalmassStandardridesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalderaCriticalmassStandardridesBundle:Default:index.html.twig', array('name' => $name));
    }
}
