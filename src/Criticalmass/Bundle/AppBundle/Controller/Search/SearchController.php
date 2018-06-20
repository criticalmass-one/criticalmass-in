<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Controller\Search;

use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Elastica\Query;
use Elastica\Query\AbstractQuery;
use Elastica\ResultSet;
use FOS\ElasticaBundle\Index\IndexManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends AbstractController
{
    protected function createQuery(string $queryPhrase, AbstractQuery $cityFilter, AbstractQuery $countryFilter): Query {
        if ($queryPhrase) {
            $simpleQueryString = new \Elastica\Query\SimpleQueryString($queryPhrase,
                ['title', 'description', 'location']);
        } else {
            $simpleQueryString = new \Elastica\Query\MatchAll();
        }

        $enabledFilter = new \Elastica\Query\Term(['isEnabled' => true]);

        $filter = new \Elastica\Query\BoolQuery();
        $filter->addFilter($enabledFilter)->addFilter($cityFilter)->addFilter($countryFilter);

        $filteredQuery = new \Elastica\Query\BoolQuery();
        $filteredQuery->addFilter($simpleQueryString)->addFilter($filter);

        $query = new \Elastica\Query($filteredQuery);

        $query->setSize(50);
        $query->addSort('_score');

        return $query;
    }

    protected function performSearch(Query $query, IndexManager $manager): ResultSet
    {
        $search = $manager->getIndex('criticalmass')->createSearch();

        //$search->addType('ride');
        //$search->addType('city');

        return $search->search($query);
    }

    protected function addAggregations(\Elastica\Query $query)
    {
        $aggregation = new \Elastica\Aggregation\Terms('city');
        $aggregation->setField('city');
        $aggregation->setSize(50);
        $query->addAggregation($aggregation);

        $aggregation = new \Elastica\Aggregation\Terms('country');
        $aggregation->setField('country');
        $aggregation->setSize(50);
        $query->addAggregation($aggregation);

        return $query;
    }

    protected function createCityFilter(array $cities = [])
    {
        $filters = [];

        foreach ($cities as $city) {
            $filters[] = new \Elastica\Query\Term(['city' => $city]);
        }

        return new \Elastica\Query\BoolOr($filters);
    }

    protected function createCountryFilter(array $countries = [])
    {
        $filters = [];

        foreach ($countries as $country) {
            $filters[] = new \Elastica\Query\Term(['country' => $country]);
        }

        return new \Elastica\Query\BoolOr($filters);
    }

    public function queryAction(Request $request, IndexManager $manager): Response
    {
        $queryPhrase = $request->get('query');
        $cities = $request->get('cities');
        $countries = $request->get('countries');

        if ($cities) {
            $cityFilter = $this->createCityFilter($cities);
        } else {
            $cityFilter = new \Elastica\Query\MatchAll();
        }

        if ($countries) {
            $countryFilter = $this->createCountryFilter($countries);
        } else {
            $countryFilter = new \Elastica\Query\MatchAll();
        }

        $query = $this->createQuery($queryPhrase, $cityFilter, $countryFilter);

        $query = $this->addAggregations($query);

        /** @var ResultSet $resultSet */
        $resultSet = $this->performSearch($query, $manager);

        $transformer = $this->get('fos_elastica.elastica_to_model_transformer.collection.criticalmass');

        $results = $transformer->transform($resultSet->getResults());

        return $this->render('AppBundle:Search:result.html.twig',
            [
                'results' => $results,
                'resultSet' => $resultSet,
                'query' => $queryPhrase

            ]
        );
    }
}
