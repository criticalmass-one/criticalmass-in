<?php

namespace Caldera\CriticalmassStatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StatisticController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalderaCriticalmassStatisticBundle:Default:index.html.twig', array('name' => $name));
    }
}
