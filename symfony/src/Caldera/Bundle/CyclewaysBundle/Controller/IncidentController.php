<?php

namespace Caldera\Bundle\CyclewaysBundle\Controller;

use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\Incident;
use Caldera\Bundle\CriticalmassSiteBundle\Controller\AbstractController;
use Caldera\Bundle\CyclewaysBundle\Form\Type\IncidentType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IncidentController extends AbstractController
{
    public function mapAction(Request $request, $citySlug)
    {
        $city = $this->getCheckedCity($citySlug);

        return $this->render(
            'CalderaCyclewaysBundle:Incident:map.html.twig',
            [
                'city' => $city
            ]
        );
    }

    public function loadAction(Request $request): Response
    {
        $finder = $this->container->get('fos_elastica.finder.criticalmass.incident');

        $topLeft = [
            'lat' => (float) $request->query->get('northWestLatitude'),
            'lon' => (float) $request->query->get('northWestLongitude')
        ];

        $bottomRight = [
            'lat' => (float) $request->query->get('southEastLatitude'),
            'lon' => (float) $request->query->get('southEastLongitude')
        ];

        $geoFilter = new \Elastica\Filter\GeoBoundingBox('pin', [$topLeft, $bottomRight]);

        $filteredQuery = new \Elastica\Query\Filtered(new \Elastica\Query\MatchAll(), $geoFilter);

        $query = new \Elastica\Query($filteredQuery);

        $query->setSize(15);

        $results = $finder->find($query);

        $serializer = $this->get('jms_serializer');
        $serializedData = $serializer->serialize($results, 'json');

        return new Response($serializedData);
    }

    public function addAction(Request $request, $citySlug)
    {
        $city = $this->getCheckedCity($citySlug);

        $incident = new Incident();
        $incident->setUser($this->getUser());
        $incident->setCity($city);

        $form = $this->createForm(
            IncidentType::class,
            $incident,
            [
                'action' => $this->generateUrl(
                    'caldera_cycleways_incident_add',
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
            'CalderaCyclewaysBundle:Incident:edit.html.twig',
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
                IncidentType::class,
                $incident,
                [
                    'action' => $this->generateUrl(
                        'caldera_cycleways_incident_add',
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
