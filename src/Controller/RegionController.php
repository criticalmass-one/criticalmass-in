<?php declare(strict_types=1);

namespace App\Controller;

use App\Repository\CityRepository;
use Symfony\Component\HttpFoundation\Response;

class RegionController extends AbstractController
{
    public function indexAction(
        CityRepository $cityRepository,
        string $slug1 = null,
        string $slug2 = null,
        string $slug3 = null
    ): Response {
        $region = null;

        if ($slug1 && $slug2 && $slug3) {
            $region = $this->getRegionRepository()->findOneBySlug($slug3);
        } elseif ($slug1 && $slug2) {
            $region = $this->getRegionRepository()->findOneBySlug($slug2);
        } elseif ($slug1) {
            $region = $this->getRegionRepository()->findOneBySlug($slug1);
        } else {
            $region = $this->getRegionRepository()->find(1);
        }

        $cities = $cityRepository->findCitiesOfRegion($region);
        $allCities = $cityRepository->findChildrenCitiesOfRegion($region);
        $regions = $this->getRegionRepository()->findByParentRegion($region);

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
