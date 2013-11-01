<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Caldera\CriticalmassStatisticBundle\Utility\Trackable;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LiveController extends Controller implements Trackable
{
    public function showAction()
    {
        return $this->render('CalderaCriticalmassDesktopBundle:Live:show.html.twig');
    }
}
