<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Abraham\TwitterOAuth\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RegionController extends AbstractController
{
    public function indexAction($slug1 = null, $slug2 = null, $slug3 = null)
    {
        $region = null;

        if ($slug1 and $slug2 and $slug3) {
            $region = $this->getRegionRepository()->findOneBySlug($slug3);
        } elseif ($slug1 and $slug2) {
            $region = $this->getRegionRepository()->findOneBySlug($slug2);
        } elseif ($slug1) {
            $region = $this->getRegionRepository()->findOneBySlug($slug1);
        } else {
            $region = $this->getRegionRepository()->find(1);
        }

        $cities = $this->getCityRepository()->findCitiesOfRegion($region);
        $allCities = $this->getCityRepository()->findChildrenCitiesOfRegion($region);
        $regions = $this->getRegionRepository()->findByParentRegion($region);

        $cityCounter = [];

        // do not name it $region as $region is already in use
        foreach ($regions as $region2) {
            $cityCounter[$region2->getId()] = $this->getCityRepository()->countChildrenCitiesOfRegion($region2);
        }

        return $this->render(
            'CalderaCriticalmassSiteBundle:Region:index.html.twig',
            [
                'region' => $region,
                'regions' => $regions,
                'cities' => $cities,
                'cityCounter' => $cityCounter,
                'allCities' => $allCities
            ]
        );
    }
}
