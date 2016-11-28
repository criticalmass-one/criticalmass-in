<?php

namespace Caldera\Bundle\CyclewaysBundle\Controller;

use Caldera\Bundle\CriticalmassSiteBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FrontpageController extends AbstractController
{
    public function indexAction(Request $request)
    {
        return $this->redirectToRoute(
            'caldera_cycleways_incident_map_city',
            [
                'citySlug' => 'hamburg'
            ]
        );
    }
}
