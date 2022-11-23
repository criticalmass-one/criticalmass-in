<?php declare(strict_types=1);

namespace App\Controller\Template;

use App\Controller\AbstractController;
use App\Entity\City;
use App\Entity\Promotion;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

class FooterController extends AbstractController
{
    public function promotionListAction(ManagerRegistry $registry): Response
    {
        $promotionList = $registry->getRepository(Promotion::class)->findBy([], ['createdAt' => 'DESC']);

        return $this->render('Template/Includes/_footer_promotion_list.html.twig', [
            'promotionList' => $promotionList,
        ]);
    }

    public function cityListAction(ManagerRegistry $registry): Response
    {
        $cityList = $registry->getRepository(City::class)->findPopularCities();

        return $this->render('Template/Includes/_footer_city_list.html.twig', [
            'cityList' => $cityList,
        ]);
    }
}
