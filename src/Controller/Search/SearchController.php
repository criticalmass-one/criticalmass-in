<?php declare(strict_types=1);

namespace App\Controller\Search;

use App\Controller\AbstractController;
use App\Repository\CityRepository;
use App\Repository\RideRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    public function __construct(
        private CityRepository $cityRepository,
        private RideRepository $rideRepository
    )
    {

    }

    #[Route('/search/query', name: 'caldera_criticalmass_search_query', priority: 260)]
    public function queryAction(Request $request): Response
    {
        $queryPhrase = $request->query->get('query', '');

        $cityResults = $this->cityRepository->searchByQuery($queryPhrase);
        $rideResults = $this->rideRepository->searchByQuery($queryPhrase);

        return $this->render('Search/result.html.twig', [
            'cityResults' => $cityResults,
            'rideResults' => $rideResults,
            'query' => $queryPhrase,
        ]);
    }
}
