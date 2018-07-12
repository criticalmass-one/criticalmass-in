<?php

namespace App\Controller\City;

use App\Controller\AbstractController;
use App\Factory\CityListFactory;
use App\Criticalmass\SeoPage\SeoPage;
use Symfony\Component\HttpFoundation\Response;

class CityListController extends AbstractController
{
    public function listAction(SeoPage $seoPage, CityListFactory $cityListFactory): Response
    {
        $seoPage->setDescription('Liste mit vielen weltweiten Critical-Mass-Radtouren.');

        return $this->render('App:CityList:list.html.twig', [
            'cityList' => $cityListFactory->getList(),
        ]);
    }
}
