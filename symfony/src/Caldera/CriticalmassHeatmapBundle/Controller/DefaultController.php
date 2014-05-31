<?php

namespace Caldera\CriticalmassHeatmapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalderaCriticalmassHeatmapBundle:Default:index.html.twig', array('name' => $name));
    }
}
