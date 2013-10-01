<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LiveController extends Controller
{
    public function showAction()
    {
        return $this->render('CalderaCriticalmassDesktopBundle:Live:show.html.twig');
    }
}
