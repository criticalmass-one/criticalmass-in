<?php

namespace App\Manager;

use App\Model\CityListModel;

class CityManager extends AbstractManager
{
    public function buildCityList(): array
    {
        $cityRepository = $this->doctrine->getRepository('App:City');
        $rideRepository = $this->doctrine->getRepository('App:Ride');

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
