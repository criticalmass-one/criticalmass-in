<?php declare(strict_types=1);

namespace App\Controller\Search;

use App\Controller\AbstractController;
use Elastica\Query;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends AbstractController
{
    protected function createQuery(string $queryPhrase): Query
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

    {

    }

    public function queryAction(Request $request): Response
    {
        $queryPhrase = $request->get('query');

        $query = $this->createQuery($queryPhrase);

        $finder = $this->get('fos_elastica.finder.criticalmass_city');

        $cityResults = $finder->find($query);

        return $this->render('Search/result.html.twig', [
            'cityResults' => $cityResults,
            'query' => $queryPhrase,
        ]);
    }
}
