<?php

namespace AppBundle\Controller\City;

use AppBundle\Controller\AbstractController;

class CityListController extends AbstractController
{
    public function listAction()
    {
        $this
            ->getSeoPage()
            ->setDescription('Liste mit vielen weltweiten Critical-Mass-Radtouren.');

        $cityManager = $this->get('app.manager.city');
        $cityList = $cityManager->buildCityList();
        /*
        $cities = $this->getCityRepository()->findCities();

        $result = [];

        foreach ($cities as $city) {
            $result[$city->getSlug()]['city'] = $city;
            $result[$city->getSlug()]['currentRide'] = $this->getRideRepository()->findCurrentRideForCity($city);
            $result[$city->getSlug()]['countRides'] = $this->getRideRepository()->countRidesByCity($city);
        }*/

        return $this->render(
            'AppBundle:CityList:list.html.twig',
            [
                'cityList' => $cityList,
            ]
        );
    }
}