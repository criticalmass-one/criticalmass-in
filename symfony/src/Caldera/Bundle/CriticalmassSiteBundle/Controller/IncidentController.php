<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\Incident;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\IncidentType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class IncidentController extends AbstractController
{
    public function listAction(Request $request, $citySlug, $rideDate = null)
    {
        $city = $this->getCheckedCity($citySlug);

        if ($rideDate) {
            $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

            $incidents = $this->getIncidentRepository()->findByRide($ride);
        } else {
            $ride = null;

            $incidents = $this->getIncidentRepository()->findByCity($city);
        }



        return $this->render(
            'CalderaCriticalmassSiteBundle:Incident:list.html.twig',
            [
                'incidents' => $incidents,
                'city' => $city,
                'ride' => $ride
            ]
        );
    }

    public function addAction(Request $request, $citySlug)
    {
        $city = $this->getCheckedCity($citySlug);

        $incident = new Incident();
        $incident->setUser($this->getUser());
        $incident->setCity($city);

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

        if ('POST' == $request->getMethod()) {
            return $this->addPostAction($request, $incident, $city, $form);
        } else {
            return $this->addGetAction($request, $incident, $city, $form);
        }
    }

    public function addGetAction(Request $request, Incident $incident, City $city, Form $form)
    {
        return $this->render(
            'CalderaCriticalmassSiteBundle:Incident:edit.html.twig',
            [
                'incident' => null,
                'city' => $city,
                'form' => $form->createView()
            ]
        );
    }

    public function addPostAction(Request $request, Incident $incident, City $city, Form $form)
    {
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

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
        }

        return $this->render(
            'CalderaCriticalmassSiteBundle:Incident:edit.html.twig',
            array(
                'incident' => $incident,
                'form' => $form->createView(),
                'city' => $city
            )
        );
    }
}
