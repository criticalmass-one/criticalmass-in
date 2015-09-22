<?php

namespace Caldera\Bundle\CyclewaysBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalderaCyclewaysBundle:Default:index.html.twig', array('name' => $name));
    }
}
