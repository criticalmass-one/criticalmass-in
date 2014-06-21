<?php

namespace Caldera\CriticalmassPlusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalderaCriticalmassPlusBundle:Default:index.html.twig', array('name' => $name));
    }
}
