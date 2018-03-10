<?php

namespace Criticalmass\Bundle\AppBundle\Controller;

use Criticalmass\Bundle\AppBundle\Entity\FacebookCityProperties;
use Criticalmass\Bundle\AppBundle\Entity\Region;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Component\SeoPage\SeoPage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StatisticController extends AbstractController
{
    public function citystatisticAction(Request $request, SeoPage $seoPage, $citySlug)
    {
        $city = $this->getCheckedCity($citySlug);

        $rides = $this->getRideRepository()->findRidesForCity($city);

        $seoPage->setDescription('Critical-Mass-Statistiken aus ' . $city->getCity() . ': Teilnehmer, Fahrtdauer, Fahrtlänge, Touren');

        return $this->render(
            'AppBundle:Statistic:city_statistic.html.twig',
            [
                'city' => $city,
                'rides' => $rides
            ]
        );
    }

    /**
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function ridestatisticAction(Ride $ride): Response
    {
        $frp = $this->getFacebookRidePropertiesRepository()->findByRide($ride);

        return $this->render('AppBundle:Statistic:ride_statistic.html.twig', [
            'ride' => $ride,
            'frp' => $frp
        ]);
    }

    public function facebookstatisticAction(Request $request)
    {
        $cityPropertiesList = $this->getFacebookCityPropertiesRepository()->findForStatistic();

        $filteredProperties = [];

        $cityList = [];
        $dayList = [];

        /**
         * @var FacebookCityProperties $facebookCityProperties
         */
        foreach ($cityPropertiesList as $facebookCityProperties) {
            $citySlug = $facebookCityProperties->getCity()->getSlug();
            $day = $facebookCityProperties->getCreatedAt()->format('Y-m-d');

            $filteredProperties[$citySlug][$day] = $facebookCityProperties;
            $dayList[$day] = $day;
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

    public function overviewAction(Request $request, SeoPage $seoPage)
    {
        /**
         * @var Region $region
         */
        $region = $this->getRegionRepository()->find(3);

        $endDateTime = new \DateTime();
        $twoYearInterval = new \DateInterval('P2Y');

        $startDateTime = new \DateTime();
        $startDateTime->sub($twoYearInterval);

        $rides = $this->getRideRepository()->findRidesInRegionInInterval($region, $startDateTime, $endDateTime);

        $citiesWithoutEstimates = $this->findCitiesWithoutParticipationEstimates($rides);
        $rides = $this->filterRideList($rides, $citiesWithoutEstimates);

        $cities = [];

        $rideMonths = [];

        /**
         * @var Ride $ride
         */
        foreach ($rides as $ride) {
            $cities[$ride->getCity()->getSlug()] = $ride->getCity();

            $rideMonths[$ride->getDateTime()->format('Y-m')] = $ride->getDateTime()->format('Y-m');
        }

        rsort($rideMonths);

        $seoPage->setDescription('Critical-Mass-Statistiken: Teilnehmer, Fahrtdauer, Fahrtlänge, Touren');

        return $this->render(
            'AppBundle:Statistic:overview.html.twig',
            [
                'cities' => $cities,
                'rides' => $rides,
                'rideMonths' => $rideMonths
            ]
        );
    }

    public function listRidesAction(Request $request, int $year, int $month): Response
    {
        $rides = $this->getRideRepository()->findEstimatedRides($year, $month);

        return $this->render(
            'AppBundle:Statistic:list_rides.html.twig',
            [
                'rides' => $rides,
            ]
        );
    }

    protected function findCitiesWithoutParticipationEstimates(array $rides)
    {
        $cityList = [];

        /**
         * @var Ride $ride
         */
        foreach ($rides as $ride) {
            if (!$ride->getEstimatedParticipants()) {
                $citySlug = $ride->getCity()->getSlug();

                if (array_key_exists($citySlug, $cityList)) {
                    ++$cityList[$citySlug];
                } else {
                    $cityList[$citySlug] = 1;
                }
            }
        }

        return $cityList;
    }

    protected function filterRideList(array $rides, array $cities)
    {
        $resultList = [];

        /**
         * @var Ride $ride
         */
        foreach ($rides as $ride) {
            $citySlug = $ride->getCity()->getSlug();

            if (!array_key_exists($citySlug, $cities) || $cities[$citySlug] < 18) {
                $resultList[] = $ride;
            }
        }

        return $resultList;
    }
}
