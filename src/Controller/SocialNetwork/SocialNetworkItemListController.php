<?php declare(strict_types=1);

namespace App\Controller\SocialNetwork;

use App\Controller\AbstractController;
use App\Criticalmass\SocialNetwork\FeedsApi\FeedItemProviderInterface;
use App\Entity\City;
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
        FeedItemProviderInterface $feedItemProvider,
    ): Response {
        $page = $request->query->getInt('page', 1);
        $items = $feedItemProvider->getFeedItemsForCity($city, $page);

        return $this->render('SocialNetwork/list_city_items.html.twig', [
            'items' => $items,
            'city' => $city,
            'page' => $page,
        ]);
    }
}
