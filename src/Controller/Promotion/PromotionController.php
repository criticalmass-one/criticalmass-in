<?php declare(strict_types=1);

namespace App\Controller\Promotion;

use App\Criticalmass\DataQuery\DataQueryManager\DataQueryManagerInterface;
use App\Criticalmass\DataQuery\RequestParameterList\QueryStringToListConverter;
use App\Entity\Promotion;
use App\Entity\Ride;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PromotionController extends AbstractController
{
    #[Route(
        '/promotion/{promotionSlug}',
        name: 'caldera_criticalmass_promotion_show',
        priority: 320
    )]
    public function showAction(
        #[MapEntity(mapping: ['promotionSlug' => 'slug'])] Promotion $promotion,
        DataQueryManagerInterface $dataQueryManager,
    ): Response
    {
        $requestParameterList = QueryStringToListConverter::convert($promotion->getQuery());

        $rideList = $dataQueryManager->query($requestParameterList, Ride::class);

        return $this->render('Promotion/index.html.twig', [
            'promotion' => $promotion,
            'rideList' => $rideList,
        ]);
    }
}
