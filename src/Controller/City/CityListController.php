<?php declare(strict_types=1);

namespace App\Controller\City;

use App\Controller\AbstractController;
use App\Criticalmass\SeoPage\SeoPageInterface;
use App\Factory\CityListFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CityListController extends AbstractController
{
    #[Route('/citylist', name: 'caldera_criticalmass_city_list', priority: 125)]
    public function listAction(SeoPageInterface $seoPage, CityListFactory $cityListFactory): Response
    {
        $seoPage->setDescription('Liste mit vielen weltweiten Critical-Mass-Radtouren.');

        return $this->render('CityList/list.html.twig', [
            'cityList' => $cityListFactory->getList(),
        ]);
    }
}
