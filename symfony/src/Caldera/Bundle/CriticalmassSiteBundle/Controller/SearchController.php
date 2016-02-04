<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Elastica\Query;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends AbstractController
{
    public function queryAction(Request $request)
    {
        $queryPhrase = $request->get('query');

        $finder = $this->container->get('fos_elastica.finder.criticalmass.city');
        
        $simpleQueryString = new \Elastica\Query\SimpleQueryString($queryPhrase, ['title', 'description', 'longDescription', 'punchLine', 'location']);

        $term = new \Elastica\Filter\Term(['isArchived' => false]);

        $filteredQuery = new \Elastica\Query\Filtered($simpleQueryString, $term);

        $query = new \Elastica\Query($filteredQuery);

        $query->setSize(50);

        $results = $finder->find($query);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Search:result.html.twig',
            [
                'query' => $queryPhrase,
                'results' => $results
            ]
        );
    }

    public function prefetchAction(Request $request)
    {
        $result = [];

        $rides = $this->getRideRepository()->findCurrentRides();

        foreach ($rides as $ride) {
            $result[] = [
                'type' => 'ride',
                'url' => $this->generateUrl($ride),
                'value' => $ride->getFancyTitle()
            ];
        }

        $cities = $this->getCityRepository()->findEnabledCities();

        foreach ($cities as $city) {
            $result[] = [
                'type' => 'city',
                'url' => $this->generateUrl($city),
                'value' => $city->getCity()
            ];
        }

        return new Response(
            json_encode($result),
            200,
            [
                'Content-Type' => 'text/json'
            ]
        );
    }
}
