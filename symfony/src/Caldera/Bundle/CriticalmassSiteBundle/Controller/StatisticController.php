<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Symfony\Component\HttpFoundation\Request;

class StatisticController extends AbstractController
{
    public function citystatisticAction(Request $request, $citySlug)
    {
        $city = $this->getCheckedCity($citySlug);

        $rides = $this->getRideRepository()->findRidesForCity($city);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Statistic:citystatistic.html.twig',
            [
                'city' => $city,
                'rides' => $rides
            ]
        );
    }

    public function overviewAction(Request $request)
    {
        $rides = $this->getRideRepository()->findRides();

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

        return $this->render(
            'CalderaCriticalmassSiteBundle:Statistic:overview.html.twig',
            [
                'cities' => $cities,
                'rides' => $rides,
                'rideMonths' => $rideMonths
            ]
        );
    }
}
