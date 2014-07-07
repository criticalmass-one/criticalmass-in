<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StatisticController extends Controller
{
    public function indexAction()
    {
        return $this->render('CalderaCriticalmassDesktopBundle:Statistic:index.html.twig');
    }
}
