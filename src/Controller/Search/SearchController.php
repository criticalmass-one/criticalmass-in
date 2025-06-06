<?php declare(strict_types=1);

namespace App\Controller\Search;

use App\Controller\AbstractController;
use App\Repository\CityRepository;
use App\Repository\RideRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends AbstractController
{
    public function __construct(
        private CityRepository $cityRepository,
        private RideRepository $rideRepository
    )
    {

    }

    public function queryAction(Request $request): Response
    {
        $queryPhrase = $request->get('query', '');

        $cityResults = $this->cityRepository->searchByQuery($queryPhrase);
        $rideResults = $this->rideRepository->searchByQuery($queryPhrase);

        return $this->render('Search/result.html.twig', [
            'cityResults' => $cityResults,
            'rideResults' => $rideResults,
            'query' => $queryPhrase,
        ]);
    }
}
