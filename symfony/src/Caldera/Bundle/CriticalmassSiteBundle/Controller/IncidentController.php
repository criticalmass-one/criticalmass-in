<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class IncidentController extends AbstractController
{
    public function listAction(Request $request, $citySlug)
    {
        $city = $this->getCheckedCity($citySlug);

        $incidents = $this->getIncidentRepository()->findByCity($city);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Incident:list.html.twig',
            [
                'incidents' => $incidents,
                'city' => $city
            ]
        );
    }
}
