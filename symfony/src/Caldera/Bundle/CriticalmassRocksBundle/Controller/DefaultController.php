<?php

namespace Caldera\Bundle\CriticalmassRocksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalderaCriticalmassRocksBundle:Default:index.html.twig', array('name' => $name));
    }
}
