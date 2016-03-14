<?php

namespace Caldera\Bundle\CalderaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalderaBundle:Default:index.html.twig', array('name' => $name));
    }
}
