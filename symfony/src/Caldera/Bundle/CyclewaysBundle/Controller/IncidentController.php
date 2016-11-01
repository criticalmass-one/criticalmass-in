<?php

namespace Caldera\Bundle\CyclewaysBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IncidentController extends Controller
{
    public function mapAction()
    {
        return $this->render('CalderaCyclewaysBundle:Incident:map.html.twig');
    }
}
