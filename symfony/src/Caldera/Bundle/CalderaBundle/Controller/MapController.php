<?php

namespace Caldera\Bundle\CalderaBundle\Controller;

use Caldera\Bundle\CriticalmassSiteBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class MapController extends AbstractController
{
    public function showCityMapAction(Request $request)
    {
        return $this->render('CalderaBundle:Map:city.html.twig');
    }
}
