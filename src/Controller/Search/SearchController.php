<?php declare(strict_types=1);

namespace App\Controller\Search;

use App\Controller\AbstractController;
use Elastica\Query;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends AbstractController
{
    public function __construct(protected TransformedFinder $cityFinder, protected TransformedFinder $rideFinder)
    {
    }

    protected function createCityQuery(string $queryPhrase): Query
    {
        if ($queryPhrase) {
            $simpleQueryString = new \Elastica\Query\SimpleQueryString($queryPhrase,
                ['title', 'description', 'location']);
        } else {
            $simpleQueryString = new \Elastica\Query\MatchAll();
        }

        $enabledFilter = new \Elastica\Query\Term(['isEnabled' => true]);

        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery
            ->addMust($enabledFilter)
            ->addMust($simpleQueryString);

        $query = new \Elastica\Query($boolQuery);

        $query->setSize(50);
        $query->addSort('_score');

        return $query;
    }

    protected function createRideQuery(string $queryPhrase): Query
    {
        if ($queryPhrase) {
            $simpleQueryString = new \Elastica\Query\SimpleQueryString($queryPhrase,
                ['title', 'description', 'location']);
        } else {
            $simpleQueryString = new \Elastica\Query\MatchAll();
        }

        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery
            ->addMust($simpleQueryString);

        $query = new \Elastica\Query($boolQuery);

        $query->setSize(50);
        $query->addSort('_score');

        return $query;
    }

    public function queryAction(Request $request): Response
    {
        $queryPhrase = $request->get('query');

        $cityQuery = $this->createCityQuery($queryPhrase);
        $cityResults = $this->cityFinder->find($cityQuery);

        $rideQuery = $this->createRideQuery($queryPhrase);
        $rideResults = $this->rideFinder->find($rideQuery);

        return $this->render('Search/result.html.twig', [
            'cityResults' => $cityResults,
            'rideResults' => $rideResults,
            'query' => $queryPhrase,
        ]);
    }
}
