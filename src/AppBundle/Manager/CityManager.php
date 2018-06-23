<?php

namespace AppBundle\Manager;

use AppBundle\Model\CityListModel;

class CityManager extends AbstractManager
{
    public function buildCityList(): array
    {
        $cityRepository = $this->doctrine->getRepository('AppBundle:City');
        $rideRepository = $this->doctrine->getRepository('AppBundle:Ride');

        $cities = $cityRepository->findCities();

        $list = [];

        foreach ($cities as $city) {
            $currentRide = $rideRepository->findCurrentRideForCity($city);
            $countRides = $rideRepository->countRidesByCity($city);

            $cityModel = new CityListModel($city, $currentRide, $countRides);

            array_push($list, $cityModel);
        }

        return $list;
    }
}
