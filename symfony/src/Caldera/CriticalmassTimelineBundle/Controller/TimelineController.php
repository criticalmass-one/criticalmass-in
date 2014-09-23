<?php

namespace Caldera\CriticalmassTimelineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TimelineController extends Controller
{
    public function listAction()
    {
        return $this->render('CalderaCriticalmassTimelineBundle:Timeline:list.html.twig');
    }
}
