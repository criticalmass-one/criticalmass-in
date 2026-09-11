<?php declare(strict_types=1);

namespace App\Controller\City;

use App\Controller\AbstractController;
use App\Criticalmass\SeoPage\SeoPageInterface;
use App\Model\CityListModel;
use App\Repository\CityRepository;
use App\Repository\CityCycleRepository;
use App\Repository\RideRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CityListController extends AbstractController
{
    #[Route('/citylist', name: 'caldera_criticalmass_city_list', priority: 125)]
    public function listAction(
        SeoPageInterface $seoPage,
        CityRepository $cityRepository,
        RideRepository $rideRepository,
        CityCycleRepository $cityCycleRepository,
    ): Response {
        $seoPage->setDescription('Liste mit vielen weltweiten Critical-Mass-Radtouren.');

        $now = new \DateTime();
        $cityList = [];
        $inactiveCityList = [];

        // Eingeschlafene Staedte bleiben erreichbar, stehen aber nicht mehr
        // gleichrangig neben denen, in denen gefahren wird.
        foreach ($cityRepository->findEnabledCities() as $city) {
            $model = new CityListModel(
                $city,
                $rideRepository->findCurrentRideForCity($city),
                $cityCycleRepository->findByCity($city, $now, $now),
                $rideRepository->countRidesByCity($city),
            );

            if ($city->isInactive()) {
                $inactiveCityList[] = $model;
            } else {
                $cityList[] = $model;
            }
        }

        return $this->render('CityList/list.html.twig', [
            'cityList' => $cityList,
            'inactiveCityList' => $inactiveCityList,
        ]);
    }
}
