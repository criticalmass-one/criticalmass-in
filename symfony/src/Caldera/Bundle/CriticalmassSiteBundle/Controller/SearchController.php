<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Elastica\Query;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends AbstractController
{
    protected function searchContent($queryPhrase)
    {
        $finder = $this->container->get('fos_elastica.finder.criticalmass.content');

        $simpleQueryString = new \Elastica\Query\SimpleQueryString($queryPhrase, ['title', 'text']);

        $term = new \Elastica\Filter\Term(['isArchived' => false]);

        $filteredQuery = new \Elastica\Query\Filtered($simpleQueryString, $term);

        $query = new \Elastica\Query($filteredQuery);

        $query->setSize(50);
        $query->setMinScore(0.5);

        return $finder->find($query);
    }

    public function queryAction(Request $request)
    {
        $queryPhrase = $request->get('query');

        $results = [];

        $results = $this->searchContent($queryPhrase);

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

        $contents = $this->getContentRepository()->findEnabledContent();

        foreach ($contents as $content) {
            $result[] = [
                'type' => 'content',
                'url' => $this->generateUrl($content),
                'value' => $content->getTitle()
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
