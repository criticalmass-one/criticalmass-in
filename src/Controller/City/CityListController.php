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

        foreach ($cityRepository->findEnabledCities() as $city) {
            $cityList[] = new CityListModel(
                $city,
                $rideRepository->findCurrentRideForCity($city),
                $cityCycleRepository->findByCity($city, $now, $now),
                $rideRepository->countRidesByCity($city),
            );
        }

        return $this->render('CityList/list.html.twig', [
            'cityList' => $cityList,
        ]);
    }
}
