<?php declare(strict_types=1);

namespace App\Controller\SocialNetwork;

use App\Controller\AbstractController;
use App\Entity\City;
use App\Entity\SocialNetworkFeedItem;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SocialNetworkItemListController extends AbstractController
{
    #[Route(
        '/{citySlug}/socialnetwork/list-items',
        name: 'criticalmass_socialnetwork_city_list_items',
        priority: 60
    )]
    public function listCityItemsAction(City $city, ManagerRegistry $registry): Response
    {
        $itemList = $registry->getRepository(SocialNetworkFeedItem::class);

        return $this->render('SocialNetwork/list_city_items.html.twig', [
            'itemList' => $itemList->findByCity($city),
            'city' => $city,
        ]);
    }
}
