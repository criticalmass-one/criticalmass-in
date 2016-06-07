<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CalderaBundle\Entity\City;
use Elastica\Query;
use Elastica\ResultSet;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends AbstractController
{
    protected function createQuery($queryPhrase)
    {
        $simpleQueryString = new \Elastica\Query\SimpleQueryString($queryPhrase, ['title', 'description', 'location']);

        $archivedFilter= new \Elastica\Filter\Term(['isArchived' => false]);

        $filteredQuery = new \Elastica\Query\Filtered($simpleQueryString, $archivedFilter);

        $query = new \Elastica\Query($filteredQuery);

        $query->setSize(50);

        return $query;
    }

    protected function performSearch(\Elastica\Query $query)
    {
        $mngr = $this->get('fos_elastica.index_manager');

        $search = $mngr->getIndex('criticalmass')->createSearch();

        $search->addType('ride');
        $search->addType('city');

        return $search->search($query);
    }

    protected function addAggregations(\Elastica\Query $query)
    {


        return $query;
    }

    public function queryAction(Request $request)
    {
        $queryPhrase = $request->get('query');

        $query = $this->createQuery($queryPhrase);

        $aggregation = new \Elastica\Aggregation\Terms('city');
        $aggregation->setField('city');
        $aggregation->setSize(50);
        $query->addAggregation($aggregation);

        $aggregation = new \Elastica\Aggregation\Terms('country');
        $aggregation->setField('country');
        $aggregation->setSize(50);
        $query->addAggregation($aggregation);

        /** @var ResultSet $resultSet */
        $resultSet = $this->performSearch($query);

        $transformer = $this->get('fos_elastica.elastica_to_model_transformer.collection.criticalmass');

        $results = $transformer->transform($resultSet->getResults());

        return $this->render('CalderaCriticalmassSiteBundle:Search:result.html.twig',
            [
                'results' => $results,
                'resultSet' => $resultSet

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
                'value' => $ride->getFancyTitle(),
                'meta' => [
                    'dateTime' => $ride->getDateTime()->format('Y-m-d\TH:i:s'),
                    'location' => ($ride->getHasLocation() ? $ride->getLocation() : '')
                ]
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
