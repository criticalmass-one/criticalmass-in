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


    protected function searchCity($queryPhrase)
    {
        $finder = $this->container->get('fos_elastica.finder.criticalmass.city');

        $simpleQueryString = new \Elastica\Query\SimpleQueryString($queryPhrase, ['city', 'title', 'description', 'longDescription', 'punchLine', 'standardLocation']);

        $archivedFilter= new \Elastica\Filter\Term(['isArchived' => false]);
        $enabledFilter = new \Elastica\Filter\Term(['isEnabled' => true]);

        $filter = new \Elastica\Filter\BoolAnd([$archivedFilter, $enabledFilter]);

        $filteredQuery = new \Elastica\Query\Filtered($simpleQueryString, $filter);

        $query = new \Elastica\Query($filteredQuery);

        $query->setSize(50);
        $query->setMinScore(0.5);

        return $finder->find($query);
    }

    protected function searchRide($queryPhrase)
    {
        $finder = $this->container->get('fos_elastica.finder.criticalmass.ride');

        $simpleQueryString = new \Elastica\Query\SimpleQueryString($queryPhrase, ['title', 'description', 'location']);

        $archivedFilter= new \Elastica\Filter\Term(['isArchived' => false]);

        $filteredQuery = new \Elastica\Query\Filtered($simpleQueryString, $archivedFilter);

        $query = new \Elastica\Query($filteredQuery);

        $query->setSize(50);
        $query->setMinScore(0.5);

        return $finder->find($query);
    }

    public function queryAction(Request $request)
    {
        $queryPhrase = $request->get('query');

        $contentResults = $this->searchContent($queryPhrase);
        $cityResults = $this->searchCity($queryPhrase);
        $rideResults = $this->searchRide($queryPhrase);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Search:result.html.twig',
            [
                'query' => $queryPhrase,
                'contentResults' => $contentResults,
                'cityResults' => $cityResults,
                'rideResults' => $rideResults,
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
