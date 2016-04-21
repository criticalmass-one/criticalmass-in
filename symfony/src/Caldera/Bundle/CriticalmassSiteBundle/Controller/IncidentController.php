<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\IncidentType;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Incident;
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

    public function addAction(Request $request, $citySlug)
    {
        $city = $this->getCheckedCity($citySlug);

        $incident = new Incident();

        $form = $this->createForm(
            new IncidentType(),
            $incident,
            [
                'action' => $this->generateUrl(
                    'caldera_criticalmass_incident_add',
                    [
                        'citySlug' => $city->getSlug()
                    ]
                )
            ]
        );

        return $this->render(
            'CalderaCriticalmassSiteBundle:Incident:add.html.twig',
            [
                'city' => $city,
                'form' => $form->createView()
            ]
        );
    }
}
