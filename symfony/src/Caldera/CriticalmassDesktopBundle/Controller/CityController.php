<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CityController extends Controller
{
    public function listAction()
    {
        return $this->render('CalderaCriticalmassDesktopBundle:City:list.html.twig');
    }
}
