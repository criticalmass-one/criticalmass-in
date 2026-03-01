<?php declare(strict_types=1);

namespace App\Controller\SocialNetwork;

use App\Controller\AbstractController;
use App\Entity\City;
use App\Repository\SocialNetworkFeedItemRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SocialNetworkItemListController extends AbstractController
{
    #[Route(
        '/{citySlug}/socialnetwork/list-items',
        name: 'criticalmass_socialnetwork_city_list_items',
        priority: 60
    )]
    public function listCityItemsAction(
        City $city,
        Request $request,
        PaginatorInterface $paginator,
        SocialNetworkFeedItemRepository $feedItemRepository,
    ): Response {
        $queryBuilder = $feedItemRepository->findByCityQueryBuilder($city);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            30
        );

        return $this->render('SocialNetwork/list_city_items.html.twig', [
            'pagination' => $pagination,
            'city' => $city,
        ]);
    }
}
