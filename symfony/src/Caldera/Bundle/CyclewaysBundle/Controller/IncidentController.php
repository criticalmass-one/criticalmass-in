<?php

namespace Caldera\Bundle\CyclewaysBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IncidentController extends Controller
{
    public function mapAction()
    {
        return $this->render('CalderaCyclewaysBundle:Incident:map.html.twig');
    }

    public function loadAction(Request $request): Response
    {
        $finder = $this->container->get('fos_elastica.finder.criticalmass.incident');

        $topLeft = [
            'lat' => (float) $request->request->get('northWestLatitude'),
            'lon' => (float) $request->request->get('northWestLongitude')
        ];

        $bottomRight = [
            'lat' => (float) $request->request->get('southEastLatitude'),
            'lon' => (float) $request->request->get('southEastLongitude')
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
}
