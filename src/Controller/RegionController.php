<?php declare(strict_types=1);

namespace App\Controller;

use App\Repository\CityRepository;
use App\Repository\RegionRepository;
use App\Repository\RideRepository;
use Symfony\Component\HttpFoundation\Response;

class RegionController extends AbstractController
{
    public function indexAction(
        CityRepository $cityRepository,
        RegionRepository $regionRepository,
        string $slug1 = null,
        string $slug2 = null,
        string $slug3 = null
    ): Response {
        $region = null;

        if ($slug1 && $slug2 && $slug3) {
            $region = $regionRepository->findOneBySlug($slug3);
        } elseif ($slug1 && $slug2) {
            $region = $regionRepository->findOneBySlug($slug2);
        } elseif ($slug1) {
            $region = $regionRepository->findOneBySlug($slug1);
        } else {
            $region = $regionRepository->find(1);
        }

        $cities = $cityRepository->findCitiesOfRegion($region);
        $allCities = $cityRepository->findChildrenCitiesOfRegion($region);
        $regions = $regionRepository->findByParentRegion($region);

        $cityCounter = [];

        // do not name it $region as $region is already in use
        foreach ($regions as $region2) {
            $cityCounter[$region2->getId()] = $cityRepository->countChildrenCitiesOfRegion($region2);
        }

        return $this->render('Region/index.html.twig', [
            'region' => $region,
            'regions' => $regions,
            'cities' => $cities,
            'cityCounter' => $cityCounter,
            'allCities' => $allCities,
        ]);
    }
}
