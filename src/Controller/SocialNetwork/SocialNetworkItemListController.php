<?php declare(strict_types=1);

namespace App\Controller\SocialNetwork;

use App\Controller\AbstractController;
use App\Entity\City;
use App\Entity\SocialNetworkFeedItem;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class SocialNetworkItemListController extends AbstractController
{
    public function listCityItemsAction(City $city, ManagerRegistry $registry): Response
    {
        $itemList = $registry->getRepository(SocialNetworkFeedItem::class);

        return $this->render('SocialNetwork/list_city_items.html.twig', [
            'itemList' => $itemList->findByCity($city),
            'city' => $city,
        ]);
    }
}
