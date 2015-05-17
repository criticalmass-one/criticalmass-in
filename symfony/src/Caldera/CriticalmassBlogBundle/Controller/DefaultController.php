<?php

namespace Caldera\CriticalmassBlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalderaCriticalmassBlogBundle:Default:index.html.twig', array('name' => $name));
    }
}
