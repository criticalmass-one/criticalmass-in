<?php

namespace Criticalmass\Bundle\AppBundle\Controller\City;

use AppBundle\Controller\AbstractController;
use AppBundle\Factory\CityListFactory;

class CityListController extends AbstractController
{
    public function listAction()
    {
        $this
            ->getSeoPage()
            ->setDescription('Liste mit vielen weltweiten Critical-Mass-Radtouren.');

        /** @var CityListFactory $cityListFactory */
        $cityListFactory = $this->get('app.factory.city_list');
        $cityList = $cityListFactory->getList();

        return $this->render(
            'AppBundle:CityList:list.html.twig',
            [
                'cityList' => $cityList,
            ]
        );
    }
}
