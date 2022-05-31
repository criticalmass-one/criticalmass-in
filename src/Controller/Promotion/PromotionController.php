<?php declare(strict_types=1);

namespace App\Controller\Promotion;

use App\Criticalmass\DataQuery\DataQueryManager\DataQueryManagerInterface;
use App\Criticalmass\DataQuery\RequestParameterList\QueryStringToListConverter;
use App\Criticalmass\ViewStorage\Cache\ViewStorageCacheInterface;
use App\Entity\Promotion;
use App\Entity\Ride;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PromotionController extends AbstractController
{
    /**
     * @ParamConverter("promotion", class="App:Promotion")
     */
    public function showAction(Promotion $promotion, DataQueryManagerInterface $dataQueryManager, ViewStorageCacheInterface $viewStorageCache): Response
    {
        $viewStorageCache->countView($promotion);
        
        $requestParameterList = QueryStringToListConverter::convert($promotion->getQuery());

        $rideList = $dataQueryManager->query($requestParameterList, Ride::class);

        return $this->render('Promotion/index.html.twig', [
            'promotion' => $promotion,
            'rideList' => $rideList,
        ]);
    }
}
