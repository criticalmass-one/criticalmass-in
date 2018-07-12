<?php

namespace AppBundle\Controller\City;

use AppBundle\Controller\AbstractController;
use AppBundle\Factory\CityListFactory;
use AppBundle\Criticalmass\SeoPage\SeoPage;
use Symfony\Component\HttpFoundation\Response;

class CityListController extends AbstractController
{
    public function listAction(SeoPage $seoPage, CityListFactory $cityListFactory): Response
    {
        $seoPage->setDescription('Liste mit vielen weltweiten Critical-Mass-Radtouren.');

        return $this->render('AppBundle:CityList:list.html.twig', [
            'cityList' => $cityListFactory->getList(),
        ]);
    }
}
