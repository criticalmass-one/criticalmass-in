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
    protected function createQuery(string $queryPhrase): Query {
        if ($queryPhrase) {
            $simpleQueryString = new \Elastica\Query\SimpleQueryString($queryPhrase,
                ['title', 'description', 'location']);
        } else {
            $simpleQueryString = new \Elastica\Query\MatchAll();
        }

        $enabledFilter = new \Elastica\Query\Term(['isEnabled' => true]);

        $filteredQuery = new \Elastica\Query\BoolQuery();
        $filteredQuery->addFilter($simpleQueryString)->addFilter($enabledFilter);

        $query = new \Elastica\Query($filteredQuery);

        $query->setSize(50);
        $query->addSort('_score');

        return $query;
    }

    protected function performSearch(Query $query, IndexManager $manager): ResultSet
    {
        $search = $manager->getIndex('criticalmass_ride')->createSearch();

        //$search->addType('ride');
        //$search->addType('city');

        return $search->search($query);
    }

    public function queryAction(Request $request, IndexManager $manager): Response
    {
        $queryPhrase = $request->get('query');

        $query = $this->createQuery($queryPhrase);
        
        /** @var ResultSet $resultSet */
        $resultSet = $this->performSearch($query, $manager);

        $transformer = $this->get('fos_elastica.elastica_to_model_transformer.collection.criticalmass_ride');

        $results = $transformer->transform($resultSet->getResults());

        return $this->render('AppBundle:Search:result.html.twig', [
            'results' => $results,
            'resultSet' => $resultSet,
            'query' => $queryPhrase,
        ]);
    }
}
