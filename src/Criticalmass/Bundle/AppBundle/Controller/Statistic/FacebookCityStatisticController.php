<?php

namespace Criticalmass\Bundle\AppBundle\Controller\Statistic;

use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Criticalmass\Bundle\AppBundle\Entity\FacebookCityProperties;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Symfony\Component\HttpFoundation\Request;

class FacebookCityStatisticController extends AbstractController
{
    public function facebookstatisticAction(Request $request)
    {
        $utc = new \DateTimeZone('UTC');
        $cityPropertiesList = $this->getFacebookCityPropertiesRepository()->findForStatistic();

        $filteredProperties = [];

        $cityList = [];
        $dayList = [];

        /**
         * @var FacebookCityProperties $facebookCityProperties
         */
        foreach ($cityPropertiesList as $facebookCityProperties) {
            $citySlug = $facebookCityProperties->getCity()->getSlug();
            $createdAt = $facebookCityProperties->getCreatedAt();

            $date = $createdAt
                ->setTimezone($utc)
                ->format('Y-m-d')
            ;

            $filteredProperties[$citySlug][$date] = $facebookCityProperties;
            $dayList[$date] = $date;
            $cityList[$citySlug] = $facebookCityProperties->getCity();
        }

        return $this->render(
            'AppBundle:Statistic:facebook_statistic.html.twig',
            [
                'cities' => $cityList,
                'filteredProperties' => $filteredProperties,
                'days' => $dayList
            ]
        );
    }
}
